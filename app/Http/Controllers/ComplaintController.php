<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\Source;
use App\Models\ReporterType;
use App\Models\UnitDestination;
use App\Models\Grade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 
use Barryvdh\DomPDF\Facade\Pdf;      
use App\Exports\ComplaintsExport;    
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ComplaintsImport; 

class ComplaintController extends Controller
{
    public function index(Request $request)
    {
        $sources = Source::all();
        $reporterTypes = ReporterType::all();
        $unitDestinations = UnitDestination::all();
        $grades = Grade::all();

        $query = Complaint::query();

        $user = Auth::user();
        if ($user->role === 'user') {
            $query->where('user_id', $user->id);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('reporter_name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('unit_destination', 'like', "%{$search}%");
            });
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('date', [$request->start_date, $request->end_date]);
        }

        if ($request->filled('unit_destination')) {
            $query->where('unit_destination', $request->unit_destination);
        }

        if ($request->sort == 'terlama') {
            $query->orderBy('date', 'asc')->orderBy('created_at', 'asc');
        } else {
            $query->orderBy('date', 'desc')->orderBy('created_at', 'desc');
        }

        $complaints = $query->paginate(10)->withQueryString();

        return view('complaints.index', compact(
            'complaints', 'sources', 'reporterTypes', 'unitDestinations', 'grades'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'inputs.*.date' => 'required|date',
            'inputs.*.reporter_type' => 'required',
            'inputs.*.source_id' => 'required',
            'inputs.*.description' => 'required',
            'inputs.*.unit_destination' => 'required',
            'inputs.*.grade' => 'required',
        ]);

        foreach ($request->inputs as $key => $value) {
            Complaint::create([
                'user_id'           => Auth::id(),
                'date'              => $value['date'],
                'reporter_type'     => $value['reporter_type'],
                'reporter_name'     => $value['reporter_name'] ?? Auth::user()->name,
                'description'       => $value['description'],
                'source_id'         => $value['source_id'],
                'unit_destination'  => $value['unit_destination'],
                'grade'             => $value['grade'],
                'status'            => 'Pending',
            ]);
        }

        return redirect()->route('complaints.index')->with('success', 'Laporan berhasil dikirim!');
    }

    public function update(Request $request, $id)
    {
        if (Auth::user()->role === 'user') {
            abort(403, 'Akses ditolak.');
        }

        $complaint = Complaint::findOrFail($id);

        $request->validate([
            'date' => 'required|date',
            'reporter_type' => 'required',
            'source_id' => 'required',
            'description' => 'required',
            'answer' => 'required',
            'unit_destination' => 'required',
            'grade' => 'required',
            'status' => 'required',
        ]);

        $complaint->update($request->all());

        return redirect()->route('complaints.index')->with('success', 'Data berhasil diperbarui!');
    }

    public function destroy($id)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Hanya Admin yang boleh menghapus data.');
        }

        Complaint::findOrFail($id)->delete();
        return redirect()->route('complaints.index')->with('success', 'Data berhasil dihapus!');
    }

    public function exportPdf()
    {
        if (Auth::user()->role === 'user') abort(403);
        $complaints = Complaint::orderBy('date', 'desc')->get();
        $pdf = Pdf::loadView('exports.pdf', compact('complaints'))->setPaper('a4', 'landscape');
        return $pdf->download('Laporan-Pengaduan-RSUD.pdf');
    }

    public function exportExcel()
    {
        if (Auth::user()->role === 'user') abort(403);
        return Excel::download(new ComplaintsExport, 'Laporan-Pengaduan-RSUD.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        try {
            Excel::import(new ComplaintsImport, $request->file('file'));
            return redirect()->route('complaints.index')->with('success', 'Data Excel berhasil di-import!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal Import: ' . $e->getMessage());
        }
    }

    /**
     * DOWNLOAD TEMPLATE EXCEL
     */
    public function downloadTemplate()
    {
        // file "template_pengaduan.xlsx" di folder public
        $path = public_path('template_pengaduan.xlsx');

        if (file_exists($path)) {
            return response()->download($path);
        } else {
            return redirect()->back()->with('error', 'File template belum tersedia di server. Harap hubungi Admin.');
        }
    }
}