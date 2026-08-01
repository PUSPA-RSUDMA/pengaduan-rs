<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WhatsappController extends Controller
{
    private $gatewayUrl = 'http://localhost:3000'; // Sesuaikan jika port gateway Anda berbeda

    // Tampilkan halaman manajemen WhatsApp
    public function index()
    {
        $statusData = ['status' => 'DISCONNECTED'];
        $qrCodeUrl = null;

        try {
            // Cek status sesi WhatsApp saat ini
            $response = Http::get("{$this->gatewayUrl}/api/sessions/default");
            if ($response->successful()) {
                $data = $response->json();
                $statusData = $data;
            }

            // Ambil gambar QR code jika status belum terkoneksi
            $qrResponse = Http::get("{$this->gatewayUrl}/api/default/auth/qr?format=image");
            if ($qrResponse->successful()) {
                // Ubah gambar binary ke base64 agar langsung tampil di tag <img> HTML
                $qrCodeUrl = 'data:image/png;base64,' . base64_encode($qrResponse->body());
            }
        } catch (\Exception $e) {
            // Gateway belum aktif atau offline
        }

        return view('whatsapp.index', compact('statusData', 'qrCodeUrl'));
    }

    // Tombol untuk Memutus Perangkat / Logout (Aman)
    public function disconnect()
    {
        try {
            Http::post("{$this->gatewayUrl}/api/sessions/default/logout");
            return back()->with('success', 'Perangkat WhatsApp berhasil diputus (Logged out).');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memutus perangkat: ' . $e->getMessage());
        }
    }

    // Tombol untuk Tes Kirim Pesan
    public function testSend(Request $request)
    {
        $request->validate([
            'nomor' => 'required',
            'pesan' => 'required',
        ]);

        $formatNomor = '62' . ltrim($request->nomor, '0');

        try {
            $response = Http::post("{$this->gatewayUrl}/api/sendText", [
                'session' => 'default',
                'chatId'  => $formatNomor . '@c.us',
                'text'    => $request->pesan,
            ]);

            if ($response->successful()) {
                return back()->with('success', 'Pesan uji coba berhasil dikirim!');
            } else {
                return back()->with('error', 'Gagal mengirim pesan: ' . $response->body());
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan koneksi ke Gateway: ' . $e->getMessage());
        }
    }
}