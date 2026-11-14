<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\Permintaan;
use App\Models\PermohonanInformasi;
use App\Models\PermohonanUhc;
use App\Models\Pkrs;
use App\Models\Lasehat;
use App\Models\Logbook;
use App\Models\User;
use App\Models\Supir;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MainDashboardController extends Controller
{
    public function index(Request $request)
    {
        // Default tahun ini & bulan (kosong = seluruh bulan di tahun tsb)
        $tahun = (int) ($request->year ?? date('Y'));
        $bulan = $request->month ?? '';

        // Helper closure untuk menerapkan filter Tahun & Bulan pada query builder
        $applyDateFilter = function ($query, $column) use ($tahun, $bulan) {
            $query->whereYear($column, $tahun);
            if (!empty($bulan)) {
                $query->whereMonth($column, $bulan);
            }
            return $query;
        };

        // ==========================================
        // 1. DATA PENGADUAN MASYARAKAT
        // ==========================================
        $pengaduanQuery   = $applyDateFilter(Complaint::query(), 'date');
        $pengaduanTotal   = (clone $pengaduanQuery)->count();
        $pengaduanPending = (clone $pengaduanQuery)->where('status', 'Pending')->count();
        $pengaduanProses  = (clone $pengaduanQuery)->where('status', 'Proses')->count();
        $pengaduanSelesai = (clone $pengaduanQuery)->where('status', 'Selesai')->count();
        $pengaduanKritis  = (clone $pengaduanQuery)->where(function ($q) {
            $q->where('grade', 'like', '%merah%')
              ->orWhere('grade', '#dc3545')
              ->orWhere('grade', '#ff0000');
        })->count();

        // ==========================================
        // 2. DATA PERMINTAAN LAYANAN & INFORMASI
        // ==========================================
        $permintaanQuery     = $applyDateFilter(Permintaan::query(), 'tgl_masuk');
        $permintaanTotal     = (clone $permintaanQuery)->count();
        $permintaanChat      = (clone $permintaanQuery)->where('metode_penyampaian', 'Chat')->count();
        $permintaanTelfon    = (clone $permintaanQuery)->where('metode_penyampaian', 'Telfon')->count();
        $permintaanJenisInfo = (clone $permintaanQuery)->where('jenis_permintaan', 'Informasi')->count();
        $permintaanJenisAdu  = (clone $permintaanQuery)->where('jenis_permintaan', 'Pengaduan')->count();

        // ==========================================
        // 3. DATA PERMOHONAN INFORMASI (DOKUMEN/PPID)
        // ==========================================
        $permohonanInfoQuery = $applyDateFilter(PermohonanInformasi::query(), 'created_at');
        $permohonanInfoTotal = (clone $permohonanInfoQuery)->count();
        $permohonanInfoTopKeperluan = (clone $permohonanInfoQuery)
            ->select('keperluan', DB::raw('count(*) as total'))
            ->whereNotNull('keperluan')
            ->groupBy('keperluan')
            ->orderByDesc('total')
            ->limit(4)
            ->get();

        // ==========================================
        // 4. DATA PERALIHAN UHC
        // ==========================================
        $uhcQuery = $applyDateFilter(PermohonanUhc::query(), 'created_at');
        $uhcTotal = (clone $uhcQuery)->count();
        $uhcSegmenStats = (clone $uhcQuery)
            ->select('segmen_kepesertaan', DB::raw('count(*) as total'))
            ->whereNotNull('segmen_kepesertaan')
            ->groupBy('segmen_kepesertaan')
            ->orderByDesc('total')
            ->get();

        // ==========================================
        // 5. DATA KEGIATAN PKRS
        // ==========================================
        $pkrsQuery   = $applyDateFilter(Pkrs::query(), 'tanggal');
        $pkrsTotal   = (clone $pkrsQuery)->count();
        $pkrsBelum   = (clone $pkrsQuery)->where('status', 'Belum')->count();
        $pkrsProses  = (clone $pkrsQuery)->where('status', 'Proses')->count();
        $pkrsSelesai = (clone $pkrsQuery)->where('status', 'Selesai')->count();

        // ==========================================
        // 6. DATA MOBIL LASEHAT (AMBULANCE)
        // ==========================================
        $lasehatQuery      = $applyDateFilter(Lasehat::query(), 'tanggal_pengantaran');
        $lasehatTotal      = (clone $lasehatQuery)->count();
        $lasehatSudahSupir = (clone $lasehatQuery)->whereNotNull('supir_ambulance')->where('supir_ambulance', '!=', '')->count();
        $lasehatBelumSupir = $lasehatTotal - $lasehatSudahSupir;

        // ==========================================
        // 7. DATA AGENDA LOGBOOK & MASTER DATA
        // ==========================================
        $logbookQuery    = $applyDateFilter(Logbook::query(), 'tanggal_acara');
        $logbookTotal    = (clone $logbookQuery)->count();
        $logbookUpcoming = Logbook::whereDate('tanggal_acara', '>=', Carbon::today())->count();
        $upcomingAgendas = Logbook::whereDate('tanggal_acara', '>=', Carbon::today())
            ->orderBy('tanggal_acara', 'asc')
            ->limit(5)
            ->get();

        $totalUsers = User::count();
        $totalSupir = Supir::count();

        // ==========================================
        // 8. GRAND TOTAL & PROPORSI KESELURUHAN
        // ==========================================
        $grandTotal = $pengaduanTotal
                    + $permintaanTotal
                    + $permohonanInfoTotal
                    + $uhcTotal
                    + $pkrsTotal
                    + $lasehatTotal
                    + $logbookTotal;

        $chartLabels = [
            'Pengaduan',
            'Permintaan Info',
            'Permohonan Dokumen',
            'Peralihan UHC',
            'Kegiatan PKRS',
            'Mobil LaSehat',
            'Agenda Logbook',
        ];

        $chartData = [
            $pengaduanTotal,
            $permintaanTotal,
            $permohonanInfoTotal,
            $uhcTotal,
            $pkrsTotal,
            $lasehatTotal,
            $logbookTotal,
        ];

        // ==========================================
        // 9. TREN BULANAN (JAN - DES) SEMUA MODUL
        // ==========================================
        $monthlySeries = [
            'pengaduan'       => array_fill(0, 12, 0),
            'permintaan'      => array_fill(0, 12, 0),
            'permohonan_info' => array_fill(0, 12, 0),
            'uhc'             => array_fill(0, 12, 0),
            'pkrs'            => array_fill(0, 12, 0),
            'lasehat'         => array_fill(0, 12, 0),
        ];

        $fillMonthly = function ($modelClass, $column, $key) use ($tahun, &$monthlySeries) {
            $rows = $modelClass::selectRaw("MONTH({$column}) as m, count(*) as total")
                ->whereYear($column, $tahun)
                ->groupBy('m')
                ->pluck('total', 'm');
            for ($m = 1; $m <= 12; $m++) {
                $monthlySeries[$key][$m - 1] = (int) ($rows[$m] ?? 0);
            }
        };

        $fillMonthly(Complaint::class, 'date', 'pengaduan');
        $fillMonthly(Permintaan::class, 'tgl_masuk', 'permintaan');
        $fillMonthly(PermohonanInformasi::class, 'created_at', 'permohonan_info');
        $fillMonthly(PermohonanUhc::class, 'created_at', 'uhc');
        $fillMonthly(Pkrs::class, 'tanggal', 'pkrs');
        $fillMonthly(Lasehat::class, 'tanggal_pengantaran', 'lasehat');

        // ==========================================
        // 10. TOP PERINGKAT PER UNIT / RUANGAN / SUPIR
        // ==========================================
        $topUnitPengaduan = (clone $pengaduanQuery)
            ->select('unit_destination', DB::raw('count(*) as total'))
            ->whereNotNull('unit_destination')
            ->where('unit_destination', '!=', '')
            ->groupBy('unit_destination')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        $topUnitPermintaan = (clone $permintaanQuery)
            ->select('unit_terkait', DB::raw('count(*) as total'))
            ->whereNotNull('unit_terkait')
            ->where('unit_terkait', '!=', '')
            ->groupBy('unit_terkait')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        $topRuanganLasehat = (clone $lasehatQuery)
            ->select('tempat_dirawat', DB::raw('count(*) as total'))
            ->whereNotNull('tempat_dirawat')
            ->where('tempat_dirawat', '!=', '')
            ->groupBy('tempat_dirawat')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        $topSupirLasehat = (clone $lasehatQuery)
            ->select('supir_ambulance', DB::raw('count(*) as total'))
            ->whereNotNull('supir_ambulance')
            ->where('supir_ambulance', '!=', '')
            ->groupBy('supir_ambulance')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        // ==========================================
        // 11. DATA AKTIVITAS TERBARU (RECENT SNAPSHOTS)
        // ==========================================
        $recentComplaints = Complaint::orderByDesc('date')->orderByDesc('id')->limit(5)->get();
        $recentPermintaan = Permintaan::orderByDesc('tgl_masuk')->orderByDesc('id')->limit(5)->get();
        $recentLasehat    = Lasehat::orderByDesc('tanggal_pengantaran')->orderByDesc('id')->limit(5)->get();
        $recentPkrs       = Pkrs::orderByDesc('tanggal')->orderByDesc('id')->limit(5)->get();
        $recentInfo       = PermohonanInformasi::orderByDesc('created_at')->limit(5)->get();
        $recentUhc        = PermohonanUhc::orderByDesc('created_at')->limit(5)->get();

        return view('utama.dashboard', compact(
            'tahun', 'bulan',
            'grandTotal', 'totalUsers', 'totalSupir',
            'pengaduanTotal', 'pengaduanPending', 'pengaduanProses', 'pengaduanSelesai', 'pengaduanKritis',
            'permintaanTotal', 'permintaanChat', 'permintaanTelfon', 'permintaanJenisInfo', 'permintaanJenisAdu',
            'permohonanInfoTotal', 'permohonanInfoTopKeperluan',
            'uhcTotal', 'uhcSegmenStats',
            'pkrsTotal', 'pkrsBelum', 'pkrsProses', 'pkrsSelesai',
            'lasehatTotal', 'lasehatSudahSupir', 'lasehatBelumSupir',
            'logbookTotal', 'logbookUpcoming', 'upcomingAgendas',
            'chartLabels', 'chartData', 'monthlySeries',
            'topUnitPengaduan', 'topUnitPermintaan', 'topRuanganLasehat', 'topSupirLasehat',
            'recentComplaints', 'recentPermintaan', 'recentLasehat', 'recentPkrs', 'recentInfo', 'recentUhc'
        ));
    }
}
