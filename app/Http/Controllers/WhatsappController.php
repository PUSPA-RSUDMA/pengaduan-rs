<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsappController extends Controller
{
    // Mengarahkan langsung ke link publik gateway Anda
    private $gatewayUrl = 'http://rsudma.id:9093';

    public function index()
    {
        $statusData = ['status' => 'DISCONNECTED'];
        $qrCodeUrl = null;
        $errorMessage = null;

        try {
            // 1. Cek status sesi WhatsApp
            $response = Http::timeout(5)->get("{$this->gatewayUrl}/api/sessions/default");
            
            if ($response->successful()) {
                $statusData = $response->json();
            } elseif ($response->status() == 404) {
                // Opsional: Jika gateway mengharuskan sesi di-start (dibuat) terlebih dahulu saat 404
                // Http::post("{$this->gatewayUrl}/api/sessions/start", ['name' => 'default']);
                // $statusData['status'] = 'DISCONNECTED';
            }

            // 2. Ambil status saat ini
            // Sesuaikan key 'status' ini dengan format JSON dari gateway Anda
            $currentStatus = $statusData['status'] ?? 'DISCONNECTED';

            // 3. HANYA fetch QR Code jika status belum WORKING/CONNECTED
            if ($currentStatus !== 'WORKING') {
                
                // CATATAN: Pastikan endpoint ini benar. 
                // Jika masih 404, coba ubah menjadi: /api/sessions/default/auth/qr?format=image
                $qrEndpoint = "{$this->gatewayUrl}/api/default/auth/qr?format=image";
                
                $qrResponse = Http::timeout(5)->get($qrEndpoint);
                
                if ($qrResponse->successful()) {
                    $qrCodeUrl = 'data:image/png;base64,' . base64_encode($qrResponse->body());
                } else {
                    $errorMessage = "Gagal mengambil QR dari Gateway (HTTP Status: " . $qrResponse->status() . "). Pastikan path URL API benar dan sesi sudah di-start.";
                }
            }

        } catch (\Exception $e) {
            $errorMessage = "Tidak dapat terhubung ke server gateway di {$this->gatewayUrl}. (Detail: " . $e->getMessage() . ")";
            Log::error($errorMessage);
        }

        return view('whatsapp.index', compact('statusData', 'qrCodeUrl', 'errorMessage'));
    }

    public function disconnect()
    {
        try {
            Http::timeout(5)->post("{$this->gatewayUrl}/api/sessions/default/logout");
            return back()->with('success', 'Perangkat WhatsApp berhasil diputus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memutus perangkat: ' . $e->getMessage());
        }
    }

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