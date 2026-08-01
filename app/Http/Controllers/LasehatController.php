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
    public function index(Request $request)
    {
        $query = Lasehat::query();

        // 1. Filter Berdasarkan Range Tanggal Pengantaran
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('tanggal_pengantaran', [$request->start_date, $request->end_date]);
        }

        // 2. Filter Berdasarkan Status Supir (Sudah / Belum Ada)
        if ($request->filled('status_supir')) {
            if ($request->status_supir == 'sudah') {
                $query->whereNotNull('supir_ambulance');
            } elseif ($request->status_supir == 'belum') {
                $query->whereNull('supir_ambulance');
            }
        }

        // Ambil data dengan paginasi dan pertahankan parameter filter
        $lasehats = $query->orderBy('created_at', 'desc')->paginate(10)->appends($request->all());
        
        $ruangans = ['GRIU', 'Lily', 'Mawar', 'Lavender', 'Kemuning', 'NICU', 'ICU', 'Dahlia', 'Tulip', 'Anyelir', 'Flamboyan', 'Raflesia', 'PICU', 'Perinatologi'];
        
        // Ambil data supir untuk dropdown modal
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
        // Menggunakan link spreadsheet yang Anda berikan
        $spreadsheetId = "1jhGvboGxJZT3xoJDZ2_fJ5lX0JCkVXTfrWABl9gANaw";
        $gid = "176229218"; // Diambil dari gid link Anda

        // Link export CSV resmi Google Sheets agar langsung mendownload data mentah
        $sheetUrl = "https://docs.google.com/spreadsheets/d/{$spreadsheetId}/export?format=csv&gid={$gid}";

        try {
            $response = \Illuminate\Support\Facades\Http::get($sheetUrl);
            
            if (!$response->successful()) {
                return back()->with('error', 'Gagal mengakses Google Sheet. Status: ' . $response->status());
            }

            $csvData = $response->body();
            
            // Cek pengaman jika terdeteksi halaman HTML (artinya akses sharing belum publik)
            if (strpos(substr($csvData, 0, 100), '<html') !== false) {
                return back()->with('error', 'Gagal! Spreadsheet masih dibatasi. Pastikan akses umum sudah diatur menjadi "Siapa saja yang memiliki link" seperti pada gambar Anda sebelumnya.');
            }

            $lines = explode(PHP_EOL, $csvData);
            $sukses = 0;
            $gagal = 0;

            // Mulai dari baris ke-1 (melewati baris ke-0 yaitu header)
            for ($i = 1; $i < count($lines); $i++) {
                if (trim($lines[$i]) == '') continue;

                $line = str_getcsv($lines[$i]);
                
                // Pastikan baris memiliki minimal 6 kolom
                if (count($line) >= 6) { 
                    $tglAntar = null;
                    
                    // Cek Kolom G (Tanggal Pengantaran) di Index 6
                    if (isset($line[6]) && trim($line[6]) != '') {
                        try {
                            $tglAntar = \Carbon\Carbon::parse(str_replace('/', '-', trim($line[6])))->format('Y-m-d');
                        } catch (\Exception $e) { }
                    }
                    
                    // Fallback ke Timestamp di Index 0 jika kolom tanggal pengantaran kosong
                    if (!$tglAntar && isset($line[0]) && trim($line[0]) != '') {
                        $timestampParts = explode(' ', trim($line[0])); 
                        try {
                            $tglAntar = \Carbon\Carbon::parse(str_replace('/', '-', trim($timestampParts[0])))->format('Y-m-d');
                        } catch (\Exception $e) { }
                    }

                    // Cek Supir di Kolom H (Ket) index 7
                    $supir = null;
                    if (isset($line[7]) && trim($line[7]) != '') {
                        $ket = trim($line[7]);
                        if (strtolower($ket) != 'gagal') {
                            $supir = $ket; 
                        }
                    }

                    // Simpan atau Update ke Database
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
                        $sukses++;
                    } else {
                        $gagal++;
                    }
                } else {
                    $gagal++;
                }
            }
            
            return back()->with('success', "Sync Selesai! Berhasil memasukkan/memperbarui $sukses data dari Google Sheet ($gagal baris dilewati).");
            
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