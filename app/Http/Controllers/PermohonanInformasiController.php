<?php

namespace App\Http\Controllers;

use App\Models\PermohonanInformasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
            'file_lampiran'=> 'required|file|mimes:pdf,jpg,jpeg,png|max:2048', 
        ]);

        $filePath = null;
        if ($request->hasFile('file_lampiran')) {
            $file = $request->file('file_lampiran');
            $fileName = time() . '_' . $file->getClientOriginalName();
            
            // Upload aman menggunakan put() ke Google Drive
            Storage::disk('google')->put($fileName, file_get_contents($file));
            $filePath = $fileName;
        }

        PermohonanInformasi::create([
            'nama_pemohon'  => $request->nama_pemohon,
            'nama_pasien'   => $request->nama_pasien,
            'no_hp'         => $request->no_hp,
            'keperluan'     => $request->keperluan,
            'file_lampiran' => $filePath, 
        ]);

        return redirect()->back()->with('success', 'Permohonan Informasi berhasil ditambahkan dan file tersimpan di Google Drive!');
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
            'file_lampiran'=> 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $filePath = $permohonan->file_lampiran;

        if ($request->hasFile('file_lampiran')) {
            // Hapus file lama di Google Drive jika ada
            if ($permohonan->file_lampiran && Storage::disk('google')->exists($permohonan->file_lampiran)) {
                Storage::disk('google')->delete($permohonan->file_lampiran);
            }

            $file = $request->file('file_lampiran');
            $fileName = time() . '_' . $file->getClientOriginalName();
            
            // Upload file baru ke Google Drive
            Storage::disk('google')->put($fileName, file_get_contents($file));
            $filePath = $fileName;
        }

        $permohonan->update([
            'nama_pemohon'  => $request->nama_pemohon,
            'nama_pasien'   => $request->nama_pasien,
            'no_hp'         => $request->no_hp,
            'keperluan'     => $request->keperluan,
            'file_lampiran' => $filePath,
        ]);

        return redirect()->route('permohonan-informasi.index')->with('success', 'Data berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $permohonan = PermohonanInformasi::findOrFail($id);

        // Hapus file dari Google Drive
        if ($permohonan->file_lampiran && Storage::disk('google')->exists($permohonan->file_lampiran)) {
            Storage::disk('google')->delete($permohonan->file_lampiran);
        }

        $permohonan->delete();

        return redirect()->back()->with('success', 'Data dan lampiran di Google Drive berhasil dihapus!');
    }
}