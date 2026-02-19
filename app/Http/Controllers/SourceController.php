<?php

namespace App\Http\Controllers;

use App\Models\Source;
use Illuminate\Http\Request;

class SourceController extends Controller
{
    // TABEL + MODAL
    public function index()
    {
        $sources = Source::all();
        // Pastikan path view ini benar: resources/views/master/sources/index.blade.php
        return view('master.sources.index', compact('sources'));
    }

    // SIMPAN DATA (Dari Modal Tambah)
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:sources,name'
        ]);
        
        Source::create($request->all());
        
        return redirect()->route('sources.index')->with('success', 'Media berhasil ditambahkan!');
    }

    // UPDATE DATA (Dari Modal Edit)
    public function update(Request $request, Source $source)
    {
        $request->validate([
            'name' => 'required|unique:sources,name,'.$source->id
        ]);
        
        $source->update($request->all());
        
        return redirect()->route('sources.index')->with('success', 'Media berhasil diperbarui!');
    }

    // HAPUS DATA
    public function destroy(Source $source)
    {
        $source->delete();
        return redirect()->route('sources.index')->with('success', 'Media berhasil dihapus!');
    }
}