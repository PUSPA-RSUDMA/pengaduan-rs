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
            // 1. Jalankan backup secara paksa lewat OS (mengatasi error command not found)
            $output = shell_exec('cd /var/www/pengaduan-rs && php artisan backup:run --only-db 2>&1');

            // 2. Cari file .zip yang baru saja dibuat oleh sistem
            $backupPath = storage_path('app/Laravel');
            
            if (!File::exists($backupPath)) {
                return back()->with('error', 'Folder backup belum terbentuk di server.');
            }

            $files = File::files($backupPath);
            
            if (empty($files)) {
                return back()->with('error', 'File backup gagal dibuat. Log terminal: ' . $output);
            }

            // Urutkan file untuk mengambil yang paling baru dibuat
            usort($files, function ($a, $b) {
                return $b->getMTime() - $a->getMTime();
            });
            
            $latestFile = $files[0];
            $filePath = $latestFile->getRealPath();
            $fileName = $latestFile->getFilename();

            // 3. Upload file zip tersebut langsung ke Google Drive
            $gdriveUrl = "https://script.google.com/macros/s/AKfycbxzFC0M_U2_lMOcqxTfd8eaU6VLDcWm0nVMply8-slAxMLeDEQc2DEo-pFo9PIlkvU/exec";

            // Waktu timeout dinaikkan ke 120 detik agar proses upload file besar tidak terputus
            $response = Http::timeout(120)->asForm()->post($gdriveUrl, [
                'data'     => base64_encode(file_get_contents($filePath)),
                'mimetype' => 'application/zip',
                'filename' => $fileName,
            ]);

            $result = $response->json();

            // 4. Validasi hasil upload
            if (isset($result['status']) && $result['status'] == 'success') {
                
                // (OPSIONAL) Hapus tanda miring ganda (//) di bawah ini jika Anda ingin 
                // file zip di server LANGSUNG DIHAPUS setelah berhasil masuk ke Google Drive
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