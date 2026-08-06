<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WhatsappController extends Controller
{
    // Tampilkan View Utama (Gabungan)
    public function index()
    {
        return view('whatsapp.index');
    }

    // 1. API Lokal: Cek Status & QR
    public function checkStatus()
    {
        try {
            $response = Http::timeout(3)->get('http://localhost:3000/api/status');
            return response()->json($response->json());
        } catch (\Exception $e) {
            return response()->json(['status' => 'ERROR', 'message' => 'Node.js server tidak aktif.']);
        }
    }

    // 2. API Lokal: Ambil Daftar Chat (Untuk Live Chat)
    public function getChats()
    {
        try {
            $response = Http::timeout(5)->get('http://localhost:3000/api/chats');
            return response()->json($response->json());
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal mengambil chat. Pastikan engine WA menyala.'], 500);
        }
    }

    // 3. API Lokal: Ambil Riwayat Pesan
    public function getMessages(Request $request)
    {
        try {
            $response = Http::timeout(5)->get('http://localhost:3000/api/messages', [
                'chatId' => $request->chatId
            ]);
            return response()->json($response->json());
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal mengambil pesan.'], 500);
        }
    }

    // 4. Kirim Pesan (Bisa via Form Uji Coba atau via Live Chat)
    public function testSend(Request $request)
    {
        $request->validate([
            'nomor' => 'required',
            'pesan' => 'required',
        ]);

        try {
            $response = Http::post('http://localhost:3000/api/send', [
                'number'  => $request->nomor,
                'message' => $request->pesan,
            ]);

            $result = $response->json();

            // Jika request berasal dari Live Chat (AJAX/Fetch)
            if ($request->wantsJson() || $request->ajax()) {
                if ($response->successful()) {
                    return response()->json(['success' => true]);
                }
                return response()->json(['error' => $result['error'] ?? 'Gagal mengirim'], 400);
            }

            // Jika request berasal dari Form Uji Coba (Submit biasa)
            if ($response->successful()) {
                return back()->with('success', 'Pesan berhasil dikirim via WhatsApp Gateway lokal!');
            } else {
                return back()->with('error', 'Gagal mengirim pesan: ' . ($result['error'] ?? 'Terjadi kesalahan'));
            }
        } catch (\Exception $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['error' => 'Koneksi ke WA Engine Gagal'], 500);
            }
            return back()->with('error', 'Gagal terhubung ke Engine WA (Pastikan Node.js menyala): ' . $e->getMessage());
        }
    }

    // 5. API Lokal: Hapus Chat
    public function deleteChat(Request $request)
    {
        try {
            $response = Http::timeout(5)->delete('http://localhost:3000/api/chats', [
                'chatId' => $request->chatId
            ]);
            return response()->json($response->json());
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal menghapus chat.'], 500);
        }
    }

    // 6. Sinkronisasi Kontak WA
    // 6. Sinkronisasi Kontak WA (Safe Mode)
    public function syncChats()
    {
        try {
            $response = Http::timeout(15)->post('http://localhost:3000/api/sync');
            $result = $response->json();

            if ($response->successful()) {
                return response()->json($result);
            } else {
                return response()->json(['success' => false, 'error' => $result['error'] ?? 'Gagal menyinkronkan kontak dari WhatsApp.'], 400);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => 'Gagal terhubung ke engine WA: ' . $e->getMessage()], 500);
        }
    }
}