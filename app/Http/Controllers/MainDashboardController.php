<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\Permintaan;
use App\Models\Lasehat;
use Illuminate\Http\Request;

class MainDashboardController extends Controller
{
    public function index(Request $request)
    {
        // Default tahun ini
        $tahun = $request->year ?? date('Y');

        // 1. DATA PENGADUAN
        $pengaduanTotal = Complaint::whereYear('date', $tahun)->count();
        $pengaduanPending = Complaint::whereYear('date', $tahun)->where('status', 'Pending')->count();
        $pengaduanSelesai = Complaint::whereYear('date', $tahun)->where('status', 'Selesai')->count();

        // 2. DATA PERMINTAAN INFORMASI
        $permintaanTotal = Permintaan::whereYear('tgl_masuk', $tahun)->count();
        $permintaanChat = Permintaan::whereYear('tgl_masuk', $tahun)->where('metode_penyampaian', 'Chat')->count();
        $permintaanTelfon = Permintaan::whereYear('tgl_masuk', $tahun)->where('metode_penyampaian', 'Telfon')->count();

        // 3. DATA LASEHAT (AMBULANCE)
        $lasehatTotal = Lasehat::whereYear('tanggal_pengantaran', $tahun)->count();
        
        // Data untuk Grafik Perbandingan (Doughnut / Bar)
        $chartLabels = ['Pengaduan', 'Permintaan Layanan', 'Mobil LaSehat'];
        $chartData = [$pengaduanTotal, $permintaanTotal, $lasehatTotal];

        // Total Interaksi Layanan Publik
        $grandTotal = $pengaduanTotal + $permintaanTotal + $lasehatTotal;

        return view('utama.dashboard', compact(
            'tahun', 
            'pengaduanTotal', 'pengaduanPending', 'pengaduanSelesai',
            'permintaanTotal', 'permintaanChat', 'permintaanTelfon',
            'lasehatTotal',
            'chartLabels', 'chartData', 'grandTotal'
        ));
    }
}