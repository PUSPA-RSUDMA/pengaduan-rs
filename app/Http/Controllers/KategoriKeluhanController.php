<?php

namespace App\Http\Controllers;

use App\Models\KategoriKeluhan;
use App\Models\ItemKeluhan;
use Illuminate\Http\Request;

class KategoriKeluhanController extends Controller
{
    public function index()
    {
        $kategoris = KategoriKeluhan::with('items')->get();
        return view('master.kategori_keluhan.index', compact('kategoris'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|unique:kategori_keluhan,name']);
        KategoriKeluhan::create($request->all());
        return redirect()->back()->with('success', 'Kategori baru berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $kategori = KategoriKeluhan::findOrFail($id);
        $request->validate(['name' => 'required|unique:kategori_keluhan,name,'.$id]);
        $kategori->update($request->all());
        return redirect()->back()->with('success', 'Kategori berhasil diupdate!');
    }

    public function destroy($id)
    {
        KategoriKeluhan::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Kategori beserta isinya berhasil dihapus!');
    }

    // -- BAGIAN ITEM DETAIL KELUHAN --
    public function storeItem(Request $request)
    {
        $request->validate([
            'kategori_keluhan_id' => 'required|exists:kategori_keluhan,id',
            'name' => 'required'
        ]);
        ItemKeluhan::create($request->all());
        return redirect()->back()->with('success', 'Pilihan detail berhasil ditambahkan!');
    }

    public function destroyItem($id)
    {
        ItemKeluhan::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Pilihan detail berhasil dihapus!');
    }

    public function updateItem(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);
        
        // Sesuaikan nama Model dengan yang Anda gunakan di project
        $item = ItemKeluhan::findOrFail($id); 
        $item->update([
            'name' => $request->name
        ]);
        
        return redirect()->back()->with('success', 'Pilihan detail keluhan berhasil diupdate!');
    }
}