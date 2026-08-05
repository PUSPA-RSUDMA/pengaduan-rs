<?php

namespace App\Http\Controllers;

use App\Models\Permintaan;
use App\Models\UnitDestination;
use App\Models\KategoriPermintaan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PermintaanExport;

class PermintaanController extends Controller
{
    public function index(Request $request)
    {
        $unitDestinations = UnitDestination::all();
        // Ambil master kategori keluhan beserta itemnya secara dinamis
        $kategoriPermintaan = KategoriPermintaan::with('items')->get();
        
        $query = Permintaan::query();

        // Fitur Pencarian
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('no_hp', 'like', "%{$search}%")
                  ->orWhere('uraian', 'like', "%{$search}%")
                  ->orWhere('unit_terkait', 'like', "%{$search}%");
            });
        }

        // Fitur Filter Tanggal Masuk
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('tgl_masuk', [$request->start_date, $request->end_date]);
        }

        // Fitur Filter Unit
        if ($request->filled('unit_terkait')) {
            $query->where('unit_terkait', $request->unit_terkait);
        }

        // Fitur Sorting
        if ($request->sort == 'terlama') {
            $query->orderBy('tgl_masuk', 'asc')->orderBy('created_at', 'asc');
        } else {
            $query->orderBy('tgl_masuk', 'desc')->orderBy('created_at', 'desc');
        }

        $permintaans = $query->paginate(10)->withQueryString();

        return view('permintaan.index', compact('permintaans', 'unitDestinations', 'kategoriPermintaan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'inputs.*.tgl_masuk' => 'required|date',
            'inputs.*.no_hp' => 'required|numeric',
            'inputs.*.metode_penyampaian' => 'required|in:Chat,Telfon',
            'inputs.*.jenis_permintaan' => 'required|in:Pengaduan,Informasi',
            'inputs.*.detail_keluhan' => 'nullable|array', // Validasi array detail keluhan
            'inputs.*.uraian' => 'required|string',
            'inputs.*.unit_terkait' => 'required|string',
            'inputs.*.tgl_verifikasi' => 'nullable|date',
        ]);

        foreach ($request->inputs as $value) {
            Permintaan::create([
                'user_id'            => Auth::id(),
                'tgl_masuk'          => $value['tgl_masuk'],
                'no_hp'              => $value['no_hp'],
                'metode_penyampaian' => $value['metode_penyampaian'],
                'jenis_permintaan'   => $value['jenis_permintaan'],
                'detail_keluhan'     => $value['detail_keluhan'] ?? null, // Simpan data checklist
                'uraian'             => $value['uraian'],
                'unit_terkait'       => $value['unit_terkait'],
                'tgl_verifikasi'     => $value['tgl_verifikasi'] ?? null,
            ]);
        }

        return redirect()->route('permintaan.index')->with('success', 'Data berhasil disimpan!');
    }

    public function update(Request $request, $id)
    {
        $permintaan = Permintaan::findOrFail($id);

        $request->validate([
            'tgl_masuk'          => 'required|date',
            'no_hp'              => 'required|numeric',
            'metode_penyampaian' => 'required|in:Chat,Telfon',
            'jenis_permintaan'   => 'required|in:Pengaduan,Informasi',
            'detail_keluhan'     => 'nullable|array',
            'uraian'             => 'required|string',
            'unit_terkait'       => 'required|string',
            'tgl_verifikasi'     => 'nullable|date',
        ]);

        $permintaan->update([
            'tgl_masuk'          => $request->tgl_masuk,
            'no_hp'              => $request->no_hp,
            'metode_penyampaian' => $request->metode_penyampaian,
            'jenis_permintaan'   => $request->jenis_permintaan,
            'detail_keluhan'     => $request->detail_keluhan, // Update data checklist
            'uraian'             => $request->uraian,
            'unit_terkait'       => $request->unit_terkait,
            'tgl_verifikasi'     => $request->tgl_verifikasi,
        ]);

        return redirect()->back()->with('success', 'Data berhasil diperbarui!');
    }

    public function destroy($id)
    {
        Permintaan::findOrFail($id)->delete();
        return redirect()->back()->route('permintaan.index')->with('success', 'Data berhasil dihapus!');
    }

    public function exportPdf(Request $request)
    {
        $permintaans = Permintaan::orderBy('tgl_masuk', 'desc')->get();
        
        $pdf = Pdf::loadView('exports.permintaan_pdf', compact('permintaans'))
                 ->setPaper('a4', 'landscape');
                 
        return $pdf->download('Layanan-Pengaduan-Informasi.pdf');
    }

    public function exportExcel(Request $request)
    {
        return Excel::download(new PermintaanExport, 'Layanan-Pengaduan-Informasi.xlsx');
    }
}