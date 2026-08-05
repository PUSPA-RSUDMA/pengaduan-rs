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
    protected $description = 'Mengirim pesan notifikasi WhatsApp H-1 untuk agenda logbook via Node.js Gateway Raspberry Pi';

    public function handle()
    {
        $besok = Carbon::tomorrow()->format('Y-m-d');
        $agendas = Logbook::where('tanggal_acara', $besok)->get();

        if ($agendas->count() > 0) {
            $nomorTujuan = '085336102800';

            foreach ($agendas as $agenda) {
                $pesan = "📢 *PENGINGAT H-1 AGENDA*\n\n"
                       . "Halo, ada agenda yang harus diurus besok:\n"
                       . "📌 *Acara:* {$agenda->judul_acara}\n"
                       . "📅 *Tanggal:* " . Carbon::parse($agenda->tanggal_acara)->format('d-m-Y') . "\n"
                       . "📝 *Deskripsi:* " . ($agenda->deskripsi ?? 'Tidak ada keterangan') . "\n\n"
                       . "Mohon segera disiapkan ya!";

                try {
                    // MENGIRIM KE API NODE.JS LOKAL DI RASPBERRY PI
                    $response = Http::post('http://localhost:3000/api/send', [
                        'number'  => $nomorTujuan,
                        'message' => $pesan,
                    ]);

                    if ($response->successful()) {
                        Log::info("Notifikasi WA H-1 berhasil dikirim ke {$nomorTujuan} untuk agenda: {$agenda->judul_acara}");
                        $this->info("Berhasil mengirim pengingat: {$agenda->judul_acara}");
                    } else {
                        Log::error("Gagal mengirim WA via Node.js ke {$nomorTujuan}: " . $response->body());
                        $this->error("Gagal mengirim pengingat untuk: {$agenda->judul_acara}");
                    }

                } catch (\Exception $e) {
                    Log::error("Terjadi kesalahan koneksi ke Node.js WA Gateway: " . $e->getMessage());
                    $this->error("Terjadi kesalahan sistem: " . $e->getMessage());
                }
            }
            
            $this->info('Semua notifikasi WhatsApp H-1 logbook telah selesai diproses.');
        } else {
            $this->info('Tidak ada agenda untuk H-1 (besok).');
        }
    }
}