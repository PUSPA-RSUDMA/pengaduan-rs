<?php

namespace App\Http\Controllers;

use App\Models\Logbook;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LogbookController extends Controller
{
    public function index()
    {
        $logbooks = Logbook::all();
        
        // Format data agar sesuai dengan format yang diminta FullCalendar
        $events = [];
        $besok = Carbon::tomorrow()->format('Y-m-d');

        foreach ($logbooks as $item) {
            $events[] = [
                'id' => $item->id,
                'title' => $item->judul_acara,
                'start' => $item->tanggal_acara,
                'description' => $item->deskripsi,
                // Jika acaranya besok (H-1), beri warna Merah. Selain itu Biru.
                'backgroundColor' => ($item->tanggal_acara === $besok) ? '#dc3545' : '#0d6efd',
                'borderColor' => ($item->tanggal_acara === $besok) ? '#dc3545' : '#0d6efd',
            ];
        }

        return view('logbook.index', compact('events'));
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

    // FUNGSI BARU UNTUK EDIT DATA
    public function update(Request $request, $id)
    {
        $request->validate([
            'judul_acara' => 'required|string|max:255',
            'tanggal_acara' => 'required|date',
        ]);

        $logbook = Logbook::findOrFail($id);
        $logbook->update($request->all());

        return back()->with('success', 'Agenda logbook berhasil diperbarui!');
    }

    public function destroy($id)
    {
        Logbook::findOrFail($id)->delete();
        return back()->with('success', 'Agenda logbook berhasil dihapus!');
    }
}