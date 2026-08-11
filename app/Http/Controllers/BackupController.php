<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\File;

class BackupController extends Controller
{
    public function backupAndUpload()
    {
        try {
            // 1. Jalankan perintah backup lewat OS
            $output = shell_exec('cd /var/www/pengaduan-rs && php artisan backup:run --only-db 2>&1');

            // 2. Cari SEMUA file .zip yang ada di dalam folder storage/app/ (tidak peduli nama foldernya apa)
            $allFiles = File::allFiles(storage_path('app'));
            
            // Saring hanya file yang berekstensi .zip
            $zipFiles = array_filter($allFiles, function ($file) {
                return $file->getExtension() === 'zip';
            });

            // Jika tidak ada file zip sama sekali, tampilkan isi $output agar kita tahu error dari terminal
            if (empty($zipFiles)) {
                return back()->with('error', 'Gagal membuat backup! Log Terminal: ' . $output);
            }

            // Urutkan file zip untuk mengambil yang PALING BARU dibuat
            usort($zipFiles, function ($a, $b) {
                return $b->getMTime() - $a->getMTime();
            });
            
            $latestFile = $zipFiles[0];
            $filePath = $latestFile->getRealPath();
            $fileName = $latestFile->getFilename();

            // 3. Upload file zip tersebut langsung ke Google Drive
            $gdriveUrl = "https://script.google.com/macros/s/AKfycbygPIXlJjabvTa9E8_YfoBoerqnkAm8i5Xzy9E_b9bENjCVKMzefdvx-rIMxXxKr3VW/exec";

            $response = Http::timeout(120)->asForm()->post($gdriveUrl, [
                'data'     => base64_encode(file_get_contents($filePath)),
                'mimetype' => 'application/zip',
                'filename' => $fileName,
            ]);

            $result = $response->json();

            // 4. Validasi hasil upload
            if (isset($result['status']) && $result['status'] == 'success') {
                
                // (OPSIONAL) Hapus tanda // di bawah ini jika ingin file di server dihapus setelah masuk GDrive
                // File::delete($filePath);

                return back()->with('success', 'Berhasil! Backup selesai dan sudah tersimpan di Google Drive.');
            } else {
                return back()->with('error', 'Gagal upload ke Google Drive: ' . ($result['message'] ?? 'Error tidak dikenal'));
            }

        } catch (\Throwable $e) {
            return back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }
}