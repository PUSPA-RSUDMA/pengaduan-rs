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
        // Set default: Tanggal 1 bulan ini sampai tanggal terakhir bulan ini
        $startDate = $request->start_date ?? Carbon::now()->startOfMonth()->format('Y-m-d');
        $endDate = $request->end_date ?? Carbon::now()->endOfMonth()->format('Y-m-d');

        // Statistik Ruangan Terbanyak (Menggunakan whereBetween untuk range tanggal)
        $ruanganStats = Lasehat::selectRaw('tempat_dirawat, count(*) as total')
            ->whereBetween('tanggal_pengantaran', [$startDate, $endDate])
            ->groupBy('tempat_dirawat')
            ->orderByDesc('total')
            ->get();

        // Statistik Supir Terbanyak
        $supirStats = Lasehat::selectRaw('supir_ambulance, count(*) as total')
            ->whereBetween('tanggal_pengantaran', [$startDate, $endDate])
            ->whereNotNull('supir_ambulance') // Hanya yang sudah diinput supirnya
            ->groupBy('supir_ambulance')
            ->orderByDesc('total')
            ->get();

        return view('lasehat.dashboard', compact('ruanganStats', 'supirStats', 'startDate', 'endDate'));
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
    public function syncGoogleSheet()
    {
        // 1. MASUKKAN URL PUBLISH CSV DI SINI
        $sheetUrl = "https://docs.google.com/spreadsheets/d/1jhGvboGxJZT3xoJDZ2_fJ5lX0JCkVXTfrWABl9gANaw/edit?resourcekey=&gid=176229218#gid=176229218";

        try {
            // Gunakan HTTP Client Laravel agar lebih stabil
            $response = \Illuminate\Support\Facades\Http::get($sheetUrl);
            
            if (!$response->successful()) {
                return back()->with('error', 'Gagal mengakses URL Google Sheet. Status: ' . $response->status());
            }

            $csvData = $response->body();
            
            // Cek apakah URL yang dimasukkan ternyata salah (Halaman HTML biasa, bukan CSV)
            if (strpos(substr($csvData, 0, 100), '<html') !== false) {
                return back()->with('error', 'Gagal! URL yang dimasukkan adalah link Web, bukan link CSV. Pastikan Anda memilih opsi "Nilai yang dipisahkan koma (.csv)" saat Publish.');
            }

            $lines = explode(PHP_EOL, $csvData);
            
            $sukses = 0;
            $gagal = 0;

            // Mulai dari baris ke-1 (melewati baris ke-0 yaitu header)
            for ($i = 1; $i < count($lines); $i++) {
                if (trim($lines[$i]) == '') continue;

                // Gunakan str_getcsv (Secara default membaca koma, cukup aman untuk CSV Google)
                $line = str_getcsv($lines[$i]);
                
                // Pastikan baris memiliki minimal 6 kolom 
                if (count($line) >= 6) { 
                    $tglAntar = null;
                    
                    // --- LOGIKA TANGGAL LEBIH FLEKSIBEL ---
                    // Coba baca kolom Tanggal Pengantaran (Index 6)
                    if (isset($line[6]) && trim($line[6]) != '') {
                        try {
                            // Pakai parse biasa agar bisa membaca format apapun (DD/MM/YYYY atau MM/DD/YYYY)
                            $tglAntar = \Carbon\Carbon::parse(str_replace('/', '-', trim($line[6])))->format('Y-m-d');
                        } catch (\Exception $e) { }
                    }
                    
                    // Fallback ke Timestamp (Index 0) jika kolom 6 kosong / gagal dibaca
                    if (!$tglAntar && isset($line[0]) && trim($line[0]) != '') {
                        $timestampParts = explode(' ', trim($line[0])); 
                        try {
                            $tglAntar = \Carbon\Carbon::parse(str_replace('/', '-', trim($timestampParts[0])))->format('Y-m-d');
                        } catch (\Exception $e) { }
                    }

                    // --- LOGIKA SUPIR ---
                    $supir = null;
                    if (isset($line[7]) && trim($line[7]) != '') {
                        $ket = trim($line[7]);
                        if (strtolower($ket) != 'gagal') {
                            $supir = $ket; 
                        }
                    }

                    // --- SIMPAN DATA KE DATABASE ---
                    if ($tglAntar && trim($line[1]) != '') {
                        Lasehat::updateOrCreate(
                            [
                                'nama_pasien' => trim($line[1]),
                                'tanggal_pengantaran' => $tglAntar,
                            ],
                            [
                                'tempat_dirawat' => trim($line[2]) ?: '-',
                                'alamat_tujuan' => trim($line[3]) ?: '-',
                                'penanggung_jawab' => trim($line[4]) ?: '-',
                                'no_telp_pj' => trim($line[5]) ?: '-',
                                'supir_ambulance' => $supir,
                                'created_by' => 'Google Form',
                            ]
                        );
                        $sukses++; // Data berhasil
                    } else {
                        $gagal++; // Gagal (biasanya karena tanggal tidak bisa di-convert)
                    }
                } else {
                    $gagal++; // Gagal karena kolomnya kurang dari 6 (baris tidak lengkap/kosong)
                }
            }
            
            // Tampilkan laporan secara spesifik
            return back()->with('success', "Sync Selesai! $sukses data baru/diupdate berhasil masuk. $gagal baris dilewati (kosong atau format tidak sesuai).");
            
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $lasehat = Lasehat::findOrFail($id);
        $lasehat->delete();

        return back()->with('success', 'Data LaSehat berhasil dihapus!');
    }
}