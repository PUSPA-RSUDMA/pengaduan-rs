<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WhatsappController extends Controller
{
    public function index()
    {
        return view('whatsapp.index');
    }

    // Mengambil status koneksi dan QR Code dari Node.js
    public function checkStatus()
    {
        try {
            $response = Http::timeout(3)->get('http://localhost:3000/api/status');
            return response()->json($response->json());
        } catch (\Exception $e) {
            return response()->json(['status' => 'ERROR', 'message' => 'Node.js server tidak aktif.']);
        }
    }

    public function testSend(Request $request)
    {
        $request->validate([
            'nomor' => 'required',
            'pesan' => 'required',
        ]);

        try {
            // MENGIRIM KE API NODE.JS LOKAL DI RASPBERRY PI
            $response = Http::post('http://localhost:3000/api/send', [
                'number'  => $request->nomor,
                'message' => $request->pesan,
            ]);

            $result = $response->json();

            if ($response->successful()) {
                return back()->with('success', 'Pesan uji coba berhasil dikirim via WhatsApp Gateway lokal!');
            } else {
                return back()->with('error', 'Gagal mengirim pesan: ' . ($result['error'] ?? 'Terjadi kesalahan'));
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal terhubung ke Engine WA (Pastikan Node.js menyala): ' . $e->getMessage());
        }
    }
}