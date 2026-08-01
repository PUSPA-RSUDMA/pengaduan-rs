<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WhatsappController extends Controller
{
    public function index()
    {
        // Langsung tampilkan view, tidak perlu cek status/barcode lagi
        return view('whatsapp.index');
    }

    public function testSend(Request $request)
    {
        $request->validate([
            'nomor' => 'required',
            'pesan' => 'required',
        ]);

        try {
            $response = Http::withHeaders([
                'Authorization' => '1ZidFLrVqRJDsK1gDbCX' // Token Fonnte Anda
            ])->post('https://api.fonnte.com/send', [
                'target' => $request->nomor,
                'message' => $request->pesan,
            ]);

            if ($response->successful()) {
                return back()->with('success', 'Pesan uji coba berhasil dikirim via Fonnte!');
            } else {
                // Menangkap pesan error dari server Fonnte jika ada
                return back()->with('error', 'Gagal mengirim pesan: ' . $response->body());
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }
}