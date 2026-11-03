<?php

namespace App\Http\Controllers;

use App\Models\MasterOutputPkrs;
use Illuminate\Http\Request;

class MasterOutputPkrsController extends Controller
{
    public function index()
    {
        $outputs = MasterOutputPkrs::orderBy('name', 'asc')->get();
        return view('master.output_pkrs.index', compact('outputs'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);
        MasterOutputPkrs::create($request->all());
        return redirect()->back()->with('success', 'Data Output PKRS berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $request->validate(['name' => 'required|string|max:255']);
        MasterOutputPkrs::findOrFail($id)->update($request->all());
        return redirect()->back()->with('success', 'Data Output PKRS berhasil diperbarui!');
    }

    public function destroy($id)
    {
        MasterOutputPkrs::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Data Output PKRS berhasil dihapus!');
    }
}