<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\File;

class BackupController extends Controller
{
    public function backupAndUpload()
{
    try {
        // Panggil perintah artisan yang baru dibuat
        // Eksekusi langsung ke sistem operasi Raspberry Pi
        $output = shell_exec('cd /var/www/pengaduan-rs && php artisan backup:run --only-db 2>&1');

        // Opsional: Jika Anda ingin melihat log error di terminal jika gagal
        if (strpos($output, 'Backup completed!') === false) {
            throw new \Exception("Gagal membuat backup: " . $output);
        }

        return back()->with('success', 'Backup database berhasil diproses dan dikirim ke WhatsApp!');

    } catch (\Throwable $e) {
        return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
    }
}
}