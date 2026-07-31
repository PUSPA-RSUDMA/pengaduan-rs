<?php

namespace App\Http\Controllers;

use App\Models\Permintaan;
use App\Models\KategoriKeluhan; // 1. Import Model Master Kategori
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

        // 5. Grafik Batang untuk Unit Terkait
        $unitData = Permintaan::selectRaw('unit_terkait, count(*) as total')
            ->whereYear('tgl_masuk', $selectedYear)
            ->groupBy('unit_terkait')
            ->orderBy('total', 'desc')
            ->get();

        $unitLabels = $unitData->pluck('unit_terkait')->toArray();
        $unitValues = $unitData->pluck('total')->toArray();

        // 6. Top Nomor HP (Paling Banyak)
        $topPhones = Permintaan::selectRaw('no_hp, count(*) as total')
            ->whereYear('tgl_masuk', $selectedYear)
            ->whereNotNull('no_hp')
            ->where('no_hp', '!=', '') 
            ->groupBy('no_hp')
            ->orderBy('total', 'desc')
            ->limit(5) 
            ->get();


        // === 7. DATA BARU: ANALISIS SUB-KATEGORI KELUHAN (DINAMIS DARI JSON & MASTER) ===
        $catMonth = $request->input('cat_month', ''); // Kosong = Semua Bulan
        $catYear  = $request->input('cat_year', $selectedYear);
        $catSort  = $request->input('cat_sort', 'desc'); // desc = terbanyak, asc = terendah

        $catQuery = Permintaan::query()->whereYear('tgl_masuk', $catYear);
        if (!empty($catMonth)) {
            $catQuery->whereMonth('tgl_masuk', $catMonth);
        }

        $permintaansCategory = $catQuery->pluck('detail_keluhan');

        // Ambil Master Kategori & Item dari Database
        $masterKategori = KategoriKeluhan::with('items')->get();
        
        $subCategoryCounts = [];

        foreach ($masterKategori as $kat) {
            $catName = $kat->name;
            $subCategoryCounts[$catName] = [];

            // Inisialisasi awal nilai item menjadi 0
            foreach ($kat->items as $item) {
                $subCategoryCounts[$catName][$item->name] = 0;
            }
        }

        // Looping data permintaan untuk menghitung frekuensi item di dalam JSON detail_keluhan
        foreach ($permintaansCategory as $detailJson) {
            if (!empty($detailJson) && is_array($detailJson)) {
                foreach ($detailJson as $catName => $items) {
                    if (isset($subCategoryCounts[$catName]) && is_array($items)) {
                        foreach ($items as $itemName) {
                            if (isset($subCategoryCounts[$catName][$itemName])) {
                                $subCategoryCounts[$catName][$itemName]++;
                            }
                        }
                    }
                }
            }
        }

        // Sorting Data berdasarkan pilihan 'cat_sort'
        foreach ($subCategoryCounts as $catName => &$counts) {
            if ($catSort == 'asc') {
                asort($counts);
            } else {
                arsort($counts);
            }
        }
        unset($counts);


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
            'unitLabels',
            'unitValues',
            'topPhones',
            // Variabel Sub-Kategori Dinamis
            'catMonth', 
            'catYear', 
            'catSort', 
            'masterKategori', 
            'subCategoryCounts'
        ));
    }
}