<?php

namespace App\Http\Controllers;

use App\Models\PermohonanInformasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Jobs\UploadToGoogleDrive;
use App\Jobs\DeleteFromGoogleDrive;

class PermohonanInformasiController extends Controller
{
    public function index()
    {
        $data = PermohonanInformasi::orderBy('created_at', 'desc')->get();
        return view('permohonan-informasi.index', compact('data'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_pemohon' => 'required|string|max:255',
            'nama_pasien'  => 'required|string|max:255',
            'no_hp'        => 'required|string|max:20',
            'keperluan'    => 'required',
            'file_lampiran'=> 'required|file|mimes:pdf,jpg,jpeg,png|max:10240', 
        ]);

        // 1. Simpan data ke DB DULU dengan lampiran bernilai tulisan sementara (opsional)
        $permohonan = PermohonanInformasi::create([
            'nama_pemohon'  => $request->nama_pemohon,
            'nama_pasien'   => $request->nama_pasien,
            'no_hp'         => $request->no_hp,
            'keperluan'     => $request->keperluan,
            'file_lampiran' => null, // Akan diupdate oleh Job nanti
        ]);

        // 2. Jika ada file, simpan di lokal dan jalankan antrean
        if ($request->hasFile('file_lampiran')) {
            $file = $request->file('file_lampiran');
            $fileName = time() . '_' . $file->getClientOriginalName();
            
            // Simpan ke disk lokal (folder: storage/app/temp) dengan sangat cepat
            $localPath = $file->storeAs('temp', $fileName, 'local');
            
            // 3. Lempar tugas upload G-Drive ke Background Job
            UploadToGoogleDrive::dispatch($permohonan->id, $localPath, $fileName);
        }

        return redirect()->back()->with('success', 'Permohonan berhasil ditambahkan. File sedang diproses di belakang layar!');
    }

    public function edit($id)
    {
        $permohonan = PermohonanInformasi::findOrFail($id);
        return view('permohonan-informasi.edit', compact('permohonan'));
    }

    public function update(Request $request, $id)
    {
        $permohonan = PermohonanInformasi::findOrFail($id);

        $request->validate([
            'nama_pemohon' => 'required|string|max:255',
            'nama_pasien'  => 'required|string|max:255',
            'no_hp'        => 'required|string|max:20',
            'keperluan'    => 'required',
            'file_lampiran'=> 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        if ($request->hasFile('file_lampiran')) {
            // 1. Lempar tugas HAPUS file lama ke background
            if ($permohonan->file_lampiran) {
                DeleteFromGoogleDrive::dispatch($permohonan->file_lampiran);
            }

            // 2. Proses file BARU
            $file = $request->file('file_lampiran');
            $fileName = time() . '_' . $file->getClientOriginalName();
            
            // Simpan sementara di lokal
            $localPath = $file->storeAs('temp', $fileName, 'local');
            
            // Set kolom di database menjadi null sementara waktu
            $permohonan->file_lampiran = null; 

            // Dispatch job upload baru
            UploadToGoogleDrive::dispatch($permohonan->id, $localPath, $fileName);
        }

        $permohonan->update([
            'nama_pemohon'  => $request->nama_pemohon,
            'nama_pasien'   => $request->nama_pasien,
            'no_hp'         => $request->no_hp,
            'keperluan'     => $request->keperluan,
            // file_lampiran jangan diupdate di sini jika ada file baru, biarkan job yang update
        ]);

        return redirect()->route('permohonan-informasi.index')->with('success', 'Data berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $permohonan = PermohonanInformasi::findOrFail($id);

        // Lempar tugas HAPUS file ke background job
        if ($permohonan->file_lampiran) {
            DeleteFromGoogleDrive::dispatch($permohonan->file_lampiran);
        }

        // Langsung hapus record database (sangat cepat)
        $permohonan->delete();

        return redirect()->back()->with('success', 'Data berhasil dihapus!');
    }
}