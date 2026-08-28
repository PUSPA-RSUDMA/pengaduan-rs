<?php

namespace App\Jobs;

use App\Models\PermohonanInformasi;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class UploadToGoogleDrive implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $permohonanId;
    protected $localFilePath;
    protected $fileName;

    public function __construct($permohonanId, $localFilePath, $fileName)
    {
        $this->permohonanId = $permohonanId;
        $this->localFilePath = $localFilePath; // path file di server lokal sementara
        $this->fileName = $fileName; // nama file tujuan di G-Drive
    }

    public function handle()
    {
        // 1. Ambil file dari local storage
        $fileContents = Storage::disk('local')->get($this->localFilePath);

        // 2. Upload ke Google Drive
        Storage::disk('google')->put($this->fileName, $fileContents);

        // 3. Update database dengan nama file Google Drive
        $permohonan = PermohonanInformasi::find($this->permohonanId);
        if ($permohonan) {
            $permohonan->update([
                'file_lampiran' => $this->fileName
            ]);
        }

        // 4. Hapus file sementara di local storage agar server tidak penuh
        Storage::disk('local')->delete($this->localFilePath);
    }
}