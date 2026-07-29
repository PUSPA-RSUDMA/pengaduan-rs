<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; 

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // === 1. PENGAMANAN (HANYA ADMIN BOLEH MASUK) ===
        if (auth()->user()->role !== 'admin') {
            return redirect()->route('complaints.index');
        }

        // === 2. DATA KARTU ATAS ===
        $query = Complaint::query();

        $total = (clone $query)->count();
        $pending = (clone $query)->where('status', 'Pending')->count();
        $process = (clone $query)->where('status', 'Proses')->count(); 
        $done = (clone $query)->where('status', 'Selesai')->count();
        
        $critical = (clone $query)->where(function($q) {
            $q->where('grade', 'like', '%merah%')
              ->orWhere('grade', '#dc3545')
              ->orWhere('grade', '#ff0000');
        })->count();

        // === 3. PERBAIKAN DROPDOWN TAHUN ===
        $minDb = Complaint::min(DB::raw('YEAR(date)')) ?? date('Y');
        $maxDb = Complaint::max(DB::raw('YEAR(date)')) ?? date('Y');

        $startRange = min($minDb, date('Y') - 4); 
        $endRange   = max($maxDb, date('Y'));     

        $availableYears = range($endRange, $startRange);

        // === 4. DATA GRAFIK BATANG (BULANAN) ===
        $selectedYear = $request->input('year', date('Y'));

        $monthlyData = (clone $query)->selectRaw('MONTH(date) as month, count(*) as total')
            ->whereYear('date', $selectedYear)
            ->groupBy('month')
            ->pluck('total', 'month');

        $dataBulanan = [];
        for ($i = 1; $i <= 12; $i++) {
            $dataBulanan[] = $monthlyData[$i] ?? 0;
        }

        // === 5. DATA GRAFIK GARIS (TREN TAHUNAN) ===
        $input1 = $request->input('start_year', date('Y') - 4);
        $input2 = $request->input('end_year', date('Y'));

        $startYear = min($input1, $input2);
        $endYear = max($input1, $input2);

        $yearlyData = (clone $query)->selectRaw('YEAR(date) as year, count(*) as total')
            ->whereYear('date', '>=', $startYear)
            ->whereYear('date', '<=', $endYear)
            ->groupBy('year')
            ->orderBy('year', 'asc')
            ->pluck('total', 'year');

        $trendLabels = [];
        $trendData = [];

        for ($y = $startYear; $y <= $endYear; $y++) {
            $trendLabels[] = $y;
            $trendData[] = $yearlyData[$y] ?? 0;
        }

        // === 6. DATA GRAFIK UNIT & MEDIA ===
        $unitData = (clone $query)->selectRaw('unit_destination, count(*) as total')
            ->whereYear('date', $selectedYear)->whereNotNull('unit_destination')
            ->groupBy('unit_destination')->orderBy('total', 'desc')->pluck('total', 'unit_destination');
        
        $unitLabels = $unitData->keys()->toArray();
        $unitValues = $unitData->values()->toArray();

        $sourceData = (clone $query)->join('sources', 'complaints.source_id', '=', 'sources.id')
            ->selectRaw('sources.name as source_name, count(complaints.id) as total')
            ->whereYear('complaints.date', $selectedYear)
            ->groupBy('sources.name')->orderBy('total', 'desc')->pluck('total', 'source_name');
        
        $sourceLabels = $sourceData->keys()->toArray();
        $sourceValues = $sourceData->values()->toArray();


        // === 7. FITUR BARU: 6 DASHBOARD / GRAFIK SUB-KATEGORI ===
        $catMonth = $request->input('cat_month', ''); // Kosong = Semua Bulan
        $catYear  = $request->input('cat_year', date('Y'));
        $catSort  = $request->input('cat_sort', 'desc'); // desc = terbanyak, asc = terendah

        $catQuery = Complaint::query()->whereYear('date', $catYear);
        if (!empty($catMonth)) {
            $catQuery->whereMonth('date', $catMonth);
        }

        $complaintsCategory = $catQuery->get([
            'keluhan_sdm', 'keluhan_sarpras', 'keluhan_administrasi', 
            'keluhan_farmasi', 'keluhan_gizi', 'keluhan_keamanan'
        ]);

        // Inisialisasi Counter Array untuk masing-masing 6 kategori
        $sdmCounts = [
            'Etika & perilaku kurang ramah' => 0,
            'Keterlambatan kehadiran' => 0,
            'Komunikasi/penjelasan kurang' => 0,
        ];
        $sarprasCounts = [
            'Fasilitas rusak (AC, Toilet, dll)' => 0,
            'Kebersihan kurang' => 0,
            'Alat medis/umum tidak lengkap' => 0,
        ];
        $adminCounts = [
            'Antrean terlalu lama' => 0,
            'Proses pendaftaran rumit' => 0,
            'Masalah BPJS/Asuransi' => 0,
        ];
        $farmasiCounts = [
            'Tunggu obat terlalu lama' => 0,
            'Stok obat kosong' => 0,
        ];
        $giziCounts = [
            'Makanan terlambat' => 0,
            'Rasa makanan hambar/dingin' => 0,
        ];
        $keamananCounts = [
            'Parkir penuh/semrawut' => 0,
            'Barang hilang' => 0,
        ];

        // Looping data dan hitung frekuensinya
        foreach ($complaintsCategory as $c) {
            if (!empty($c->keluhan_sdm) && is_array($c->keluhan_sdm)) {
                foreach ($c->keluhan_sdm as $item) if (isset($sdmCounts[$item])) $sdmCounts[$item]++;
            }
            if (!empty($c->keluhan_sarpras) && is_array($c->keluhan_sarpras)) {
                foreach ($c->keluhan_sarpras as $item) if (isset($sarprasCounts[$item])) $sarprasCounts[$item]++;
            }
            if (!empty($c->keluhan_administrasi) && is_array($c->keluhan_administrasi)) {
                foreach ($c->keluhan_administrasi as $item) if (isset($adminCounts[$item])) $adminCounts[$item]++;
            }
            if (!empty($c->keluhan_farmasi) && is_array($c->keluhan_farmasi)) {
                foreach ($c->keluhan_farmasi as $item) if (isset($farmasiCounts[$item])) $farmasiCounts[$item]++;
            }
            if (!empty($c->keluhan_gizi) && is_array($c->keluhan_gizi)) {
                foreach ($c->keluhan_gizi as $item) if (isset($giziCounts[$item])) $giziCounts[$item]++;
            }
            if (!empty($c->keluhan_keamanan) && is_array($c->keluhan_keamanan)) {
                foreach ($c->keluhan_keamanan as $item) if (isset($keamananCounts[$item])) $keamananCounts[$item]++;
            }
        }

        // Sorting Data berdasarkan filter 'cat_sort' (Terbanyak/Terendah)
        $sortFunc = $catSort == 'asc' ? 'asort' : 'arsort';
        $sortFunc($sdmCounts);
        $sortFunc($sarprasCounts);
        $sortFunc($adminCounts);
        $sortFunc($farmasiCounts);
        $sortFunc($giziCounts);
        $sortFunc($keamananCounts);

        return view('dashboard', compact(
            'total', 'pending', 'process', 'done', 'critical',
            'availableYears', 'selectedYear', 'dataBulanan',
            'trendLabels', 'trendData', 'startYear', 'endYear',
            'unitLabels', 'unitValues', 'sourceLabels', 'sourceValues',
            // Variabel 6 Kategori 
            'catMonth', 'catYear', 'catSort',
            'sdmCounts', 'sarprasCounts', 'adminCounts', 'farmasiCounts', 'giziCounts', 'keamananCounts'
        ));
    }
}