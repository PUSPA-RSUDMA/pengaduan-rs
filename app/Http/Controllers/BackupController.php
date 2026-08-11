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
            // 1. Jalankan perintah backup database bawaan Spatie Laravel
            Artisan::call('backup:run --only-db');

            // 2. Cari file .zip terbaru yang baru saja dibuat di folder storage
            $backupPath = storage_path('app/Laravel');
            $files = File::files($backupPath);
            
            if (empty($files)) {
                return back()->with('error', 'File backup tidak ditemukan.');
            }

            // Ambil file zip terbaru
            usort($files, function ($a, $b) {
                return $b->getMTime() - $a->getMTime();
            });
            $latestFile = $files[0];
            $filePath = $latestFile->getRealPath();
            $fileName = $latestFile->getFilename();

            // 3. Siapkan data untuk dikirim ke Web App Google Drive yang sudah dibuat
            $gdriveUrl = "https://script.google.com/macros/s/AKfycbygPIXlJjabvTa9E8_YfoBoerqnkAm8i5Xzy9E_b9bENjCVKMzefdvx-rIMxXxKr3VW/exec";

            $response = Http::timeout(60)->asForm()->post($gdriveUrl, [
                'data'     => base64_encode(file_get_contents($filePath)),
                'mimetype' => 'application/zip',
                'filename' => $fileName,
            ]);

            $result = $response->json();

            if (isset($result['status']) && $result['status'] == 'success') {
                return back()->with('success', 'Backup database berhasil di-upload otomatis ke Google Drive!');
            } else {
                return back()->with('error', 'Gagal mengunggah ke Google Drive: ' . ($result['message'] ?? 'Kesalahan tidak dikenal'));
            }

        } catch (\Throwable $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}