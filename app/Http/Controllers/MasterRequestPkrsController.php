<?php

namespace App\Http\Controllers;

use App\Models\MasterRequestPkrs;
use Illuminate\Http\Request;

class MasterRequestPkrsController extends Controller
{
    public function index()
    {
        $requests = MasterRequestPkrs::orderBy('name', 'asc')->get();
        return view('master.request_pkrs.index', compact('requests'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);
        MasterRequestPkrs::create($request->all());
        return redirect()->back()->with('success', 'Data Request PKRS berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $request->validate(['name' => 'required|string|max:255']);
        MasterRequestPkrs::findOrFail($id)->update($request->all());
        return redirect()->back()->with('success', 'Data Request PKRS berhasil diperbarui!');
    }

    public function destroy($id)
    {
        MasterRequestPkrs::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Data Request PKRS berhasil dihapus!');
    }
}