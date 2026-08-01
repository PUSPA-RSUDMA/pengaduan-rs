<?php

namespace App\Http\Controllers;

use App\Models\Lasehat;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LasehatController extends Controller
{
    // === 1. TAMPILAN DASHBOARD ===
    public function dashboard(Request $request)
    {
        $year = $request->year ?? date('Y');
        $month = $request->month ?? date('m');

        // Statistik Ruangan Terbanyak
        $ruanganStats = Lasehat::selectRaw('tempat_dirawat, count(*) as total')
            ->whereYear('tanggal_pengantaran', $year)
            ->whereMonth('tanggal_pengantaran', $month)
            ->groupBy('tempat_dirawat')
            ->orderByDesc('total')
            ->get();

        // Statistik Supir Terbanyak
        $supirStats = Lasehat::selectRaw('supir_ambulance, count(*) as total')
            ->whereYear('tanggal_pengantaran', $year)
            ->whereMonth('tanggal_pengantaran', $month)
            ->whereNotNull('supir_ambulance') // Hanya yang sudah diinput supirnya
            ->groupBy('supir_ambulance')
            ->orderByDesc('total')
            ->get();

        return view('lasehat.dashboard', compact('ruanganStats', 'supirStats', 'year', 'month'));
    }

    // === 2. TAMPILAN DATA & INPUT ===
    public function index()
    {
        $lasehats = Lasehat::orderBy('created_at', 'desc')->paginate(10);
        $ruangans = ['GRIU', 'Lily', 'Mawar', 'Lavender', 'Kemuning', 'NICU', 'ICU', 'Dahlia', 'Tulip', 'Anyelir', 'Flamboyan', 'Raflesia', 'PICU', 'Perinatologi'];
        
        return view('lasehat.index', compact('lasehats', 'ruangans'));
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $data['created_by'] = auth()->user()->name ?? 'System'; // Jika login
        Lasehat::create($data);

        return back()->with('success', 'Data LaSehat berhasil ditambahkan!');
    }

    public function updateSupir(Request $request, $id)
    {
        $lasehat = Lasehat::findOrFail($id);
        $lasehat->update(['supir_ambulance' => $request->supir_ambulance]);

        return back()->with('success', 'Supir Ambulance berhasil ditugaskan!');
    }

    // === 3. API WEBHOOK UNTUK GOOGLE FORM ===
    public function webhookGoogleForm(Request $request)
    {
        // Menerima POST data dari Google Form (melalui Apps Script)
        Lasehat::create([
            'nama_pasien' => $request->nama_pasien,
            'tempat_dirawat' => $request->tempat_dirawat,
            'alamat_tujuan' => $request->alamat_tujuan,
            'tanggal_pengantaran' => $request->tanggal_pengantaran,
            'penanggung_jawab' => $request->penanggung_jawab,
            'no_telp_pj' => $request->no_telp_pj,
            'created_by' => 'Google Form', // Penanda sumber data
        ]);

        return response()->json(['status' => 'success', 'message' => 'Data diterima dari Google Form']);
    }
}