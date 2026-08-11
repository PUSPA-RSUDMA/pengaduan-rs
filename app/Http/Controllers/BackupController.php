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
        Artisan::call('backup:run', ['--only-db' => true]);

        return back()->with('success', 'Backup database berhasil diproses dan dikirim ke WhatsApp!');

    } catch (\Throwable $e) {
        return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
    }
}
}