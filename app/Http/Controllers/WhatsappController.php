<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WhatsappController extends Controller
{
    private $gatewayUrl = 'http://rsudma.id:9093';

    public function index()
    {
        return view('whatsapp.index');
    }

    // Fungsi uji coba kirim pesan tetap dipertahankan agar otomatis terkirim ke nomor tujuan
    public function testSend(Request $request)
    {
        $request->validate([
            'nomor' => 'required',
            'pesan' => 'required',
        ]);

        $formatNomor = '62' . ltrim($request->nomor, '0');

        try {
            $response = Http::timeout(10)->post("{$this->gatewayUrl}/api/sendText", [
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