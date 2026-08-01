<?php

namespace App\Http\Controllers;

use App\Models\Logbook;
use Illuminate\Http\Request;

class LogbookController extends Controller
{
    public function index()
    {
        $logbooks = Logbook::orderBy('tanggal_acara', 'asc')->paginate(10);
        return view('logbook.index', compact('logbooks'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul_acara' => 'required|string|max:255',
            'tanggal_acara' => 'required|date',
        ]);

        Logbook::create($request->all());

        return back()->with('success', 'Agenda logbook berhasil ditambahkan!');
    }

    public function destroy($id)
    {
        Logbook::findOrFail($id)->delete();
        return back()->with('success', 'Agenda logbook berhasil dihapus!');
    }
}