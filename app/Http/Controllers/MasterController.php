<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Source;
use App\Models\ReporterType;
use App\Models\UnitDestination;
use App\Models\Grade;

class MasterController extends Controller
{
    // MASTER UNIT TUJUAN
    public function unitIndex()
    {
        $units = UnitDestination::all();
        return view('master.units.index', compact('units'));
    }

    public function unitStore(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);
        UnitDestination::create($request->all());
        return back()->with('success', 'Unit Tujuan berhasil ditambahkan!');
    }

    // UPDATE
    public function unitUpdate(Request $request, $id)
    {
        $request->validate(['name' => 'required|string|max:255']);
        UnitDestination::findOrFail($id)->update(['name' => $request->name]);
        return back()->with('success', 'Data berhasil diperbarui!');
    }

    public function unitDestroy($id)
    {
        UnitDestination::findOrFail($id)->delete();
        return back()->with('success', 'Data berhasil dihapus!');
    }

    // MASTER UNIT PELAPOR
    public function reporterIndex()
    {
        $reporters = ReporterType::all();
        return view('master.reporters.index', compact('reporters'));
    }

    public function reporterStore(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);
        ReporterType::create($request->all());
        return back()->with('success', 'Unit Pelapor berhasil ditambahkan!');
    }

    public function reporterUpdate(Request $request, $id)
    {
        $request->validate(['name' => 'required|string|max:255']);
        ReporterType::findOrFail($id)->update(['name' => $request->name]);
        return back()->with('success', 'Data berhasil diperbarui!');
    }

    public function reporterDestroy($id)
    {
        ReporterType::findOrFail($id)->delete();
        return back()->with('success', 'Data berhasil dihapus!');
    }

    // MASTER KEGAWATAN (GRADE)
    public function gradeIndex()
    {
        $grades = Grade::all();
        return view('master.grades.index', compact('grades'));
    }

    public function gradeStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'color_class' => 'required', 
        ]);

        $data = $request->all();
        $data['sla_hours'] = 0; 

        Grade::create($data);
        return back()->with('success', 'Level Kegawatan berhasil ditambahkan!');
    }

    // UPDATE)
    public function gradeUpdate(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'color_class' => 'required', 
        ]);
        
        Grade::findOrFail($id)->update([
            'name' => $request->name,
            'color_class' => $request->color_class
        ]);

        return back()->with('success', 'Data berhasil diperbarui!');
    }

    public function gradeDestroy($id)
    {
        Grade::findOrFail($id)->delete();
        return back()->with('success', 'Data berhasil dihapus!');
    }
}