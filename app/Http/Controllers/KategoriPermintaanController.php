<?php

namespace App\Http\Controllers;

use App\Models\KategoriPermintaan;
use App\Models\ItemPermintaan;
use Illuminate\Http\Request;

class KategoriPermintaanController extends Controller
{
    public function index()
    {
        $kategoris = KategoriPermintaan::with('items')->get();
        return view('master.kategori_permintaan.index', compact('kategoris'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|unique:kategori_permintaan,name']);
        KategoriPermintaan::create($request->all());
        return redirect()->back()->with('success', 'Kategori Permintaan berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $kategori = KategoriPermintaan::findOrFail($id);
        $request->validate(['name' => 'required|unique:kategori_permintaan,name,'.$id]);
        $kategori->update($request->all());
        return redirect()->back()->with('success', 'Kategori Permintaan berhasil diupdate!');
    }

    public function destroy($id)
    {
        KategoriPermintaan::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Kategori beserta isinya berhasil dihapus!');
    }

    // -- BAGIAN ITEM DETAIL PERMINTAAN --
    public function storeItem(Request $request)
    {
        $request->validate([
            'kategori_permintaan_id' => 'required|exists:kategori_permintaan,id',
            'name' => 'required'
        ]);
        ItemPermintaan::create($request->all());
        return redirect()->back()->with('success', 'Pilihan detail permintaan berhasil ditambahkan!');
    }

    public function destroyItem($id)
    {
        ItemPermintaan::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Pilihan detail permintaan berhasil dihapus!');
    }

    public function updateItem(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);
        
        $item = ItemPermintaan::findOrFail($id);
        $item->update([
            'name' => $request->name
        ]);
        
        return redirect()->back()->with('success', 'Pilihan detail permintaan berhasil diupdate!');
    }
}