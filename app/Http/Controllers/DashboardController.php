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
        // Logika: Jika role user yang login BUKAN 'admin' (berarti dia staff atau user biasa),
        // Maka langsung tendang (redirect) ke halaman Data Keluhan.
        if (auth()->user()->role !== 'admin') {
            return redirect()->route('complaints.index');
        }

        // === 2. DATA KARTU ATAS ===
        // Kita tidak perlu lagi filter 'if role == user' karena user sudah ditendang di atas.
        // Jadi query ini murni melihat semua data (karena yang lolos kesini cuma Admin).
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
        
        // Cari tahun paling kecil dan paling besar di database
        $minDb = Complaint::min(DB::raw('YEAR(date)'));
        $maxDb = Complaint::max(DB::raw('YEAR(date)'));

        // Jika DB kosong, pakai tahun ini
        $minDb = $minDb ?? date('Y');
        $maxDb = $maxDb ?? date('Y');

        // Range 5 tahun terakhir s/d tahun terdepan di DB
        $startRange = min($minDb, date('Y') - 4); 
        $endRange   = max($maxDb, date('Y'));     

        // Array tahun dari Besar ke Kecil [2034, ..., 2022]
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

        // PAKSA LOGIKA: Kiri Kecil, Kanan Besar (Biar grafik tidak error)
        $startYear = min($input1, $input2);
        $endYear = max($input1, $input2);

        // Query Data Tahunan
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

        return view('dashboard', compact(
            'total', 'pending', 'process', 'done', 'critical',
            'availableYears', 'selectedYear', 'dataBulanan',
            'trendLabels', 'trendData', 'startYear', 'endYear'
        ));
    }
}