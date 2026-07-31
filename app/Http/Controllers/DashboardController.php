<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\KategoriKeluhan;
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


        // === 7. FITUR GRAFIK SUB-KATEGORI (OTOMATIS & DINAMIS) ===
        $catMonth = $request->input('cat_month', ''); // Kosong = Semua Bulan
        $catYear  = $request->input('cat_year', date('Y'));
        $catSort  = $request->input('cat_sort', 'desc'); // desc = terbanyak, asc = terendah

        $catQuery = Complaint::query()->whereYear('date', $catYear);
        if (!empty($catMonth)) {
            $catQuery->whereMonth('date', $catMonth);
        }

        $complaintsCategory = $catQuery->pluck('detail_keluhan');

        // Ambil Master Kategori & Item dari Database secara dinamis
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

        // Looping data pengaduan untuk menghitung frekuensi kemunculan item
        foreach ($complaintsCategory as $detailJson) {
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

        // Sorting Data berdasarkan filter 'cat_sort' (Terbanyak/Terendah)
        foreach ($subCategoryCounts as $catName => &$counts) {
            if ($catSort == 'asc') {
                asort($counts);
            } else {
                arsort($counts);
            }
        }
        unset($counts);

        return view('dashboard', compact(
            'total', 'pending', 'process', 'done', 'critical',
            'availableYears', 'selectedYear', 'dataBulanan',
            'trendLabels', 'trendData', 'startYear', 'endYear',
            'unitLabels', 'unitValues', 'sourceLabels', 'sourceValues',
            'catMonth', 'catYear', 'catSort', 'masterKategori', 'subCategoryCounts'
        ));
    }
}