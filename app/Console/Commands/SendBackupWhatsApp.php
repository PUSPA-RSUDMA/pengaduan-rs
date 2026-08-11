<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;

class SendBackupWhatsApp extends Command
{
    protected $signature = 'backup:send-wa';
    protected $description = 'Membuat backup database dan mengirimkannya ke WhatsApp';

    public function handle()
    {
        $this->info('Membuat backup database...');
        
        // 1. Jalankan backup Spatie
        Artisan::call('backup:run --only-db');

        // 2. Cari file zip terbaru
        $backupPath = storage_path('app/Laravel');
        $files = File::files($backupPath);
        
        if (empty($files)) {
            $this->error('File backup tidak ditemukan!');
            return;
        }

        usort($files, function ($a, $b) {
            return $b->getMTime() - $a->getMTime();
        });
        
        $latestFile = $files[0];
        $filePath = $latestFile->getRealPath();
        $fileName = $latestFile->getFilename();

        $this->info('Mengirim file ke WhatsApp...');

        // 3. Kirim ke WhatsApp Engine (Node.js) yang ada di server Anda
        // Sesuaikan nomor tujuan admin (format: 628xxxxxxxxxx)
        $targetNumber = '6285704558256'; 

        try {
            // Mengirim pesan teks konfirmasi dulu
            Http::post('http://localhost:3000/send-message', [
                'number'  => $targetNumber,
                'message' => "📂 *Backup Database RSUD Berhasil!*\nFile: {$fileName}\nWaktu: " . now()
            ]);

            $this->info('Backup berhasil diproses!');
        } catch (\Exception $e) {
            $this->error('Gagal mengirim ke WA: ' . $e->getMessage());
        }
    }
}