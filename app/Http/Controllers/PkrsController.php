<?php

namespace App\Http\Controllers;

use App\Models\Pkrs;
use App\Models\MasterRequestPkrs;
use App\Models\MasterOutputPkrs;
use App\Exports\PkrsExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PkrsController extends Controller
{
    public function index(Request $request)
    {
        $requests = MasterRequestPkrs::orderBy('name', 'asc')->get();
        $outputs = MasterOutputPkrs::orderBy('name', 'asc')->get();
        
        $query = Pkrs::query();

        // Pencarian dibungkus agar tidak bentrok dengan filter lain
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('tema', 'like', "%{$request->search}%")
                  ->orWhere('rencana_kegiatan', 'like', "%{$request->search}%");
            });
        }

        // Filter berdasarkan Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Fitur Urutkan (Sorting) - Default terbaru (desc)
        $sort = $request->input('sort', 'desc');
        $query->orderBy('tanggal', $sort === 'asc' ? 'asc' : 'desc');

        $dataPkrs = $query->paginate(10)->withQueryString();

        return view('pkrs.index', compact('dataPkrs', 'requests', 'outputs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'tema' => 'required|string',
            'rencana_kegiatan' => 'required|string',
            'request_by' => 'required|string',
            'output' => 'required|string',
            'bukti' => 'nullable|string',
            'status' => 'required|in:Belum,Proses,Selesai',
        ]);

        // Otomatis mengambil nama hari dari tanggal
        Carbon::setLocale('id');
        $hari = Carbon::parse($request->tanggal)->translatedFormat('l');

        Pkrs::create([
            'tanggal' => $request->tanggal,
            'hari' => $hari,
            'tema' => $request->tema,
            'rencana_kegiatan' => $request->rencana_kegiatan,
            'request_by' => $request->request_by,
            'output' => $request->output,
            'bukti' => $request->bukti,
            'status' => $request->status,
        ]);

        return redirect()->back()->with('success', 'Data kegiatan PKRS berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $pkrs = Pkrs::findOrFail($id);
        
        Carbon::setLocale('id');
        $hari = Carbon::parse($request->tanggal)->translatedFormat('l');

        $pkrs->update([
            'tanggal' => $request->tanggal,
            'hari' => $hari,
            'tema' => $request->tema,
            'rencana_kegiatan' => $request->rencana_kegiatan,
            'request_by' => $request->request_by,
            'output' => $request->output,
            'bukti' => $request->bukti,
            'status' => $request->status,
        ]);

        return redirect()->back()->with('success', 'Data kegiatan PKRS berhasil diperbarui!');
    }

    public function destroy($id)
    {
        Pkrs::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Data berhasil dihapus!');
    }

    public function exportExcel(Request $request)
    {
        $fileName = 'Laporan_Kegiatan_PKRS_' . date('Y-m-d_His') . '.xlsx';
        return Excel::download(
            new PkrsExport($request->search, $request->status, $request->input('sort', 'desc')),
            $fileName
        );
    }

    public function dashboard(Request $request)
    {
        $selectedYear = $request->year ?? date('Y');
        $availableYears = Pkrs::selectRaw('YEAR(tanggal) as year')->distinct()->orderBy('year', 'desc')->pluck('year');
        if($availableYears->isEmpty()) $availableYears = collect([date('Y')]);

        // Statistik Cards
        $totalKegiatan = Pkrs::whereYear('tanggal', $selectedYear)->count();
        $totalBelum = Pkrs::whereYear('tanggal', $selectedYear)->where('status', 'Belum')->count();
        $totalProses = Pkrs::whereYear('tanggal', $selectedYear)->where('status', 'Proses')->count();
        $totalSelesai = Pkrs::whereYear('tanggal', $selectedYear)->where('status', 'Selesai')->count();

        // Data Chart Kurva Bulanan
        $dataBulanan = [];
        for ($i = 1; $i <= 12; $i++) {
            $dataBulanan[] = Pkrs::whereYear('tanggal', $selectedYear)->whereMonth('tanggal', $i)->count();
        }

        // Data Chart Bar Request By
        $requestData = Pkrs::whereYear('tanggal', $selectedYear)
            ->select('request_by', DB::raw('count(*) as total'))
            ->groupBy('request_by')
            ->pluck('total', 'request_by')->toArray();
            
        // Data Chart Output
        $outputData = Pkrs::whereYear('tanggal', $selectedYear)
            ->select('output', DB::raw('count(*) as total'))
            ->groupBy('output')
            ->pluck('total', 'output')->toArray();

        return view('pkrs.dashboard', compact(
            'selectedYear', 'availableYears', 'totalKegiatan', 'totalBelum', 
            'totalProses', 'totalSelesai', 'dataBulanan', 'requestData', 'outputData'
        ));
    }
}