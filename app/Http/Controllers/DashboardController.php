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

        // === 6. DATA GRAFIK UNIT TUJUAN (Berdasarkan Tahun) ===
        $unitData = (clone $query)->selectRaw('unit_destination, count(*) as total')
            ->whereYear('date', $selectedYear)
            ->whereNotNull('unit_destination')
            ->groupBy('unit_destination')
            ->orderBy('total', 'desc')
            ->pluck('total', 'unit_destination');

        $unitLabels = $unitData->keys()->toArray();
        $unitValues = $unitData->values()->toArray();

        // === 7. DATA GRAFIK TIPE PELAPOR (Berdasarkan Tahun) ===
        $sourceData = (clone $query)
            ->join('sources', 'complaints.source_id', '=', 'sources.id')
            ->selectRaw('sources.name as source_name, count(complaints.id) as total')
            ->whereYear('complaints.date', $selectedYear)
            ->groupBy('sources.name')
            ->orderBy('total', 'desc')
            ->pluck('total', 'source_name');

        $sourceLabels = $sourceData->keys()->toArray();
        $sourceValues = $sourceData->values()->toArray();

        // === 8. FITUR BARU: GRAFIK KATEGORI KELUHAN (SDM, SARPRAS, DLL) ===
        // Ambil filter dari request
        $catMonth = $request->input('cat_month', ''); // Kosong = Semua Bulan
        $catYear  = $request->input('cat_year', date('Y'));
        $catSort  = $request->input('cat_sort', 'desc'); // desc = terbanyak, asc = terendah

        // Query khusus kategori
        $catQuery = Complaint::query()->whereYear('date', $catYear);
        if (!empty($catMonth)) {
            $catQuery->whereMonth('date', $catMonth);
        }

        // Ambil data untuk dihitung array JSON-nya
        $complaintsCategory = $catQuery->get([
            'keluhan_sdm', 'keluhan_sarpras', 'keluhan_administrasi', 
            'keluhan_farmasi', 'keluhan_gizi', 'keluhan_keamanan'
        ]);

        $catCounts = [
            'SDM & Petugas' => 0,
            'Sarana Prasarana' => 0,
            'Administrasi' => 0,
            'Farmasi' => 0,
            'Gizi & Makanan' => 0,
            'Keamanan' => 0,
        ];

        // Hitung total issue yang dicentang di setiap kategori
        foreach ($complaintsCategory as $c) {
            if (!empty($c->keluhan_sdm) && is_array($c->keluhan_sdm)) $catCounts['SDM & Petugas'] += count($c->keluhan_sdm);
            if (!empty($c->keluhan_sarpras) && is_array($c->keluhan_sarpras)) $catCounts['Sarana Prasarana'] += count($c->keluhan_sarpras);
            if (!empty($c->keluhan_administrasi) && is_array($c->keluhan_administrasi)) $catCounts['Administrasi'] += count($c->keluhan_administrasi);
            if (!empty($c->keluhan_farmasi) && is_array($c->keluhan_farmasi)) $catCounts['Farmasi'] += count($c->keluhan_farmasi);
            if (!empty($c->keluhan_gizi) && is_array($c->keluhan_gizi)) $catCounts['Gizi & Makanan'] += count($c->keluhan_gizi);
            if (!empty($c->keluhan_keamanan) && is_array($c->keluhan_keamanan)) $catCounts['Keamanan'] += count($c->keluhan_keamanan);
        }

        // Lakukan pengurutan (Sort)
        if ($catSort == 'asc') {
            asort($catCounts); // Terendah
        } else {
            arsort($catCounts); // Terbanyak
        }

        $catLabels = array_keys($catCounts);
        $catValues = array_values($catCounts);

        return view('dashboard', compact(
            'total', 'pending', 'process', 'done', 'critical',
            'availableYears', 'selectedYear', 'dataBulanan',
            'trendLabels', 'trendData', 'startYear', 'endYear',
            'unitLabels', 'unitValues', 
            'sourceLabels', 'sourceValues',
            // Variabel Kategori Baru
            'catMonth', 'catYear', 'catSort', 'catLabels', 'catValues'
        ));
    }
}