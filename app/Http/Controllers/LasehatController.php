<?php

namespace App\Http\Controllers;

use App\Models\Lasehat;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Supir;

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
        
        // Ambil data supir untuk dropdown
        $supirs = Supir::orderBy('nama_supir', 'asc')->get(); 
        
        return view('lasehat.index', compact('lasehats', 'ruangans', 'supirs'));
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $data['created_by'] = auth()->user()->name ?? 'System';
        Lasehat::create($data);

        return back()->with('success', 'Data LaSehat berhasil ditambahkan secara manual!');
    }

    public function updateSupir(Request $request, $id)
    {
        $lasehat = Lasehat::findOrFail($id);
        $lasehat->update(['supir_ambulance' => $request->supir_ambulance]);

        return back()->with('success', 'Supir Ambulance berhasil ditugaskan!');
    }

    // === FITUR SYNC DARI GOOGLE SPREADSHEET ===
    // === FITUR SYNC DARI GOOGLE SPREADSHEET ===
    public function syncGoogleSheet()
    {
        /* 
         PENTING: 
         1. Publish to Web dari Google Sheet as CSV
         2. Masukkan URL CSV tersebut di bawah ini
        */
        $sheetUrl = "https://docs.google.com/spreadsheets/d/1jhGvboGxJZT3xoJDZ2_fJ5lX0JCkVXTfrWABl9gANaw/edit?usp=sharing";

        try {
            $csvData = file_get_contents($sheetUrl);
            $lines = explode(PHP_EOL, $csvData);

            // Mulai dari baris ke-1 (melewati baris ke-0 yaitu header)
            for ($i = 1; $i < count($lines); $i++) {
                // Abaikan jika baris kosong
                if (trim($lines[$i]) == '') continue;

                $line = str_getcsv($lines[$i]);
                
                // Pastikan array memiliki setidaknya 6 indeks yang valid
                if (count($line) >= 6) { 
                    /* PENCOCOKAN INDEKS BERDASARKAN GAMBAR:
                     * $line[0] = Timestamp 
                     * $line[1] = Nama Pasien
                     * $line[2] = Ruangan Tempat Dirawat
                     * $line[3] = Alamat Tujuan Pengantaran
                     * $line[4] = Penanggung Jawab Pasien
                     * $line[5] = No.Telp Penanggung Jawab Pasien
                     * $line[6] = Tanggal Pengantaran 
                     * $line[7] = Ket (Status/Supir) 
                     */

                    // --- LOGIKA TANGGAL PENGANTARAN ---
                    $tglAntar = null;
                    // Cek apakah kolom G (Tanggal Pengantaran) diisi
                    if (isset($line[6]) && trim($line[6]) != '') {
                        try {
                            $tglAntar = Carbon::createFromFormat('d/m/Y', trim($line[6]))->format('Y-m-d');
                        } catch (\Exception $e) {
                            $tglAntar = null; // Gagal parse, lanjut ke fallback
                        }
                    }
                    
                    // Fallback: Jika kolom G kosong, ambil tanggal dari kolom A (Timestamp)
                    if (!$tglAntar && isset($line[0]) && trim($line[0]) != '') {
                        // Timestamp bentuknya "22/04/2024 13:20:31" -> kita split berdasarkan spasi
                        $timestampParts = explode(' ', trim($line[0])); 
                        try {
                            $tglAntar = Carbon::createFromFormat('d/m/Y', $timestampParts[0])->format('Y-m-d');
                        } catch (\Exception $e) {
                            continue; // Jika gagal ekstrak tanggal, skip baris ini
                        }
                    }

                    // --- LOGIKA SUPIR ---
                    $supir = null;
                    // Cek apakah kolom H (Ket) ada isinya dan bukan "gagal"
                    if (isset($line[7]) && trim($line[7]) != '') {
                        $ket = trim($line[7]);
                        if (strtolower($ket) != 'gagal') {
                            $supir = $ket; // Asumsi ini adalah nama supir
                        }
                    }

                    // Jangan input jika pasien kosong atau tgl antar gagal didapatkan
                    if ($tglAntar && trim($line[1]) != '') {
                        Lasehat::updateOrCreate(
                            [
                                'nama_pasien' => trim($line[1]),
                                'tanggal_pengantaran' => $tglAntar,
                            ],
                            [
                                'tempat_dirawat' => trim($line[2]),
                                'alamat_tujuan' => trim($line[3]),
                                'penanggung_jawab' => trim($line[4]),
                                'no_telp_pj' => trim($line[5]),
                                'supir_ambulance' => $supir, // Akan terisi otomatis jika ada nama di Kolom "Ket"
                                'created_by' => 'Google Form',
                            ]
                        );
                    }
                }
            }
            return back()->with('success', 'Sinkronisasi dengan Google Spreadsheet Berhasil!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal Sinkronisasi: ' . $e->getMessage() . '. Pastikan URL G-Sheet benar (format CSV).');
        }
    }
}