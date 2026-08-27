<?php

namespace App\Http\Controllers;

use App\Models\PermohonanUhc;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PermohonanUhcController extends Controller
{
    public function index()
    {
        $data = PermohonanUhc::orderBy('created_at', 'desc')->get();
        return view('permohonan-uhc.index', compact('data'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_pemohon'       => 'required|string|max:255',
            'nama_pasien'        => 'required|string|max:255',
            'no_hp'              => 'required|string|max:20',
            'segmen_kepesertaan' => 'required|string|max:255',
            'alasan_peralihan'   => 'required|string',
            'file_lampiran'      => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240', 
        ]);

        $filePath = null;
        if ($request->hasFile('file_lampiran')) {
            $file = $request->file('file_lampiran');
            $fileName = time() . '_UHC_' . $file->getClientOriginalName();
            
            Storage::disk('google')->put($fileName, file_get_contents($file));
            $filePath = $fileName;
        }

        PermohonanUhc::create([
            'nama_pemohon'       => $request->nama_pemohon,
            'nama_pasien'        => $request->nama_pasien,
            'no_hp'              => $request->no_hp,
            'segmen_kepesertaan' => $request->segmen_kepesertaan,
            'alasan_peralihan'   => $request->alasan_peralihan,
            'file_lampiran'      => $filePath, 
        ]);

        return redirect()->back()->with('success', 'Permohonan UHC berhasil ditambahkan dan file tersimpan di Google Drive!');
    }

    public function edit($id)
    {
        $permohonan = PermohonanUhc::findOrFail($id);
        return view('permohonan-uhc.edit', compact('permohonan'));
    }

    public function update(Request $request, $id)
    {
        $permohonan = PermohonanUhc::findOrFail($id);

        $request->validate([
            'nama_pemohon'       => 'required|string|max:255',
            'nama_pasien'        => 'required|string|max:255',
            'no_hp'              => 'required|string|max:20',
            'segmen_kepesertaan' => 'required|string|max:255',
            'alasan_peralihan'   => 'required|string',
            'file_lampiran'      => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        $filePath = $permohonan->file_lampiran;

        if ($request->hasFile('file_lampiran')) {
            if ($permohonan->file_lampiran && Storage::disk('google')->exists($permohonan->file_lampiran)) {
                Storage::disk('google')->delete($permohonan->file_lampiran);
            }

            $file = $request->file('file_lampiran');
            $fileName = time() . '_UHC_' . $file->getClientOriginalName();
            
            Storage::disk('google')->put($fileName, file_get_contents($file));
            $filePath = $fileName;
        }

        $permohonan->update([
            'nama_pemohon'       => $request->nama_pemohon,
            'nama_pasien'        => $request->nama_pasien,
            'no_hp'              => $request->no_hp,
            'segmen_kepesertaan' => $request->segmen_kepesertaan,
            'alasan_peralihan'   => $request->alasan_peralihan,
            'file_lampiran'      => $filePath,
        ]);

        return redirect()->route('permohonan-uhc.index')->with('success', 'Data UHC berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $permohonan = PermohonanUhc::findOrFail($id);

        if ($permohonan->file_lampiran && Storage::disk('google')->exists($permohonan->file_lampiran)) {
            Storage::disk('google')->delete($permohonan->file_lampiran);
        }

        $permohonan->delete();

        return redirect()->back()->with('success', 'Data UHC dan lampiran di Google Drive berhasil dihapus!');
    }
}