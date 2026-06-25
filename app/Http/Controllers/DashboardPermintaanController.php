<?php

namespace App\Http\Controllers;

use App\Models\Permintaan;
use Illuminate\Http\Request;

class DashboardPermintaanController extends Controller
{
    public function index(Request $request)
    {
        // 1. Ambil Tahun yang Tersedia dari Database (berdasarkan tgl_masuk)
        $availableYears = Permintaan::selectRaw('YEAR(tgl_masuk) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        if ($availableYears->isEmpty()) {
            $availableYears = collect([date('Y')]);
        }

        // Tahun yang dipilih filter
        $selectedYear = $request->year ?? date('Y');

        // 2. Hitung Total Data untuk Kartu Atas
        $totalChat = Permintaan::whereYear('tgl_masuk', $selectedYear)->where('metode_penyampaian', 'Chat')->count();
        $totalTelfon = Permintaan::whereYear('tgl_masuk', $selectedYear)->where('metode_penyampaian', 'Telfon')->count();
        
        $totalPengaduan = Permintaan::whereYear('tgl_masuk', $selectedYear)->where('jenis_permintaan', 'Pengaduan')->count();
        $totalInformasi = Permintaan::whereYear('tgl_masuk', $selectedYear)->where('jenis_permintaan', 'Informasi')->count();

        // 3. Persiapkan Data untuk Chart Doughnut
        $metodeLabels = ['Chat', 'Telfon'];
        $metodeValues = [$totalChat, $totalTelfon];

        $jenisLabels = ['Pengaduan', 'Informasi'];
        $jenisValues = [$totalPengaduan, $totalInformasi];

        // 4. Data Kurva/Tren Bulanan (Line Chart)
        $bulanan = Permintaan::selectRaw('MONTH(tgl_masuk) as month, count(*) as total')
            ->whereYear('tgl_masuk', $selectedYear)
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        $dataBulanan = [];
        for ($i = 1; $i <= 12; $i++) {
            $dataBulanan[] = $bulanan[$i] ?? 0;
        }

        // 5. DATA BARU: Grafik Batang untuk Unit Terkait
        // Dikelompokkan berdasarkan nama unit dan diurutkan dari yang terbanyak
        $unitData = Permintaan::selectRaw('unit_terkait, count(*) as total')
            ->whereYear('tgl_masuk', $selectedYear)
            ->groupBy('unit_terkait')
            ->orderBy('total', 'desc')
            ->get();

        $unitLabels = $unitData->pluck('unit_terkait')->toArray();
        $unitValues = $unitData->pluck('total')->toArray();

        return view('permintaan.dashboard', compact(
            'availableYears', 
            'selectedYear', 
            'totalChat', 
            'totalTelfon', 
            'totalPengaduan', 
            'totalInformasi',
            'metodeLabels', 
            'metodeValues', 
            'jenisLabels', 
            'jenisValues', 
            'dataBulanan',
            'unitLabels', // <--- Variabel baru
            'unitValues'  // <--- Variabel baru
        ));
    }
}