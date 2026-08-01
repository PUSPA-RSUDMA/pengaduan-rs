<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Logbook;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendLogbookReminder extends Command
{
    protected $signature = 'logbook:send-reminder';
    protected $description = 'Mengirim pesan notifikasi WhatsApp H-1 untuk agenda logbook';

    public function handle()
    {
        // Cari acara yang tanggal pelaksanaannya adalah BESOK (H-1)
        $besok = Carbon::tomorrow()->format('Y-m-d');
        $agendas = Logbook::where('tanggal_acara', $besok)->get();

        if ($agendas->count() > 0) {
            // Nomor tujuan yang Anda minta
            $nomorTujuan = '085336102800';
            
            // Ubah format nomor HP 08... menjadi 628... untuk standar WhatsApp
            $formatNomor = '62' . substr($nomorTujuan, 1); 

            foreach ($agendas as $agenda) {
                // Susun format pesan WhatsApp yang rapi
                $pesan = "📢 *PENGINGAT H-1 AGENDA LASEHAT*\n\n"
                       . "Halo, ada agenda yang harus diurus besok:\n"
                       . "📌 *Acara:* {$agenda->judul_acara}\n"
                       . "📅 *Tanggal:* " . Carbon::parse($agenda->tanggal_acara)->format('d-m-Y') . "\n"
                       . "📝 *Deskripsi:* " . ($agenda->deskripsi ?? 'Tidak ada keterangan') . "\n\n"
                       . "Mohon segera disiapkan ya!";

                try {
                    // CONTOH Koneksi ke WhatsApp Gateway lokal (Misal menggunakan WAHA / Baileys di port 3000)
                    // Anda juga bisa mengganti URL ini dengan API Gateway pihak ketiga gratisan jika ada.
                    $response = Http::post('http://localhost:3000/api/sendText', [
                        'chatId' => $formatNomor . '@c.us',
                        'text'   => $pesan,
                    ]);

                    if ($response->successful()) {
                        Log::info("Notifikasi WhatsApp H-1 berhasil dikirim ke {$nomorTujuan} untuk agenda: {$agenda->judul_acara}");
                    } else {
                        Log::error("Gagal mengirim WhatsApp ke {$nomorTujuan}: " . $response->body());
                    }

                } catch (\Exception $e) {
                    Log::error("Terjadi kesalahan koneksi WhatsApp: " . $e->getMessage());
                }
            }
            
            $this->info('Notifikasi WhatsApp H-1 logbook berhasil diproses.');
        } else {
            $this->info('Tidak ada agenda untuk H-1 besok.');
        }
    }
}