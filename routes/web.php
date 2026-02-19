<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// === PANGGIL CONTROLLER DI SINI ===
use App\Http\Controllers\SourceController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController; 
use App\Http\Controllers\MasterController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::resource('complaints', ComplaintController::class);
Route::get('complaints/{id}/print', [ComplaintController::class, 'print'])->name('complaints.print');

// === JALUR IMPORT EXCEL ===
Route::post('complaints/import', [ComplaintController::class, 'import'])->name('complaints.import');

// === JALUR DOWNLOAD TEMPLATE EXCEL ===
Route::get('complaints/download-template', [ComplaintController::class, 'downloadTemplate'])->name('complaints.template');

Route::get('/', function () {
    return redirect()->route('login');
});

// Jalur Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// GRUP WAJIB LOGIN
Route::middleware('auth')->group(function () {
    
    // FITUR PROFIL (DATA DIRI)
    Route::get('/profil', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profil', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profil', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // === (GANTI PASSWORD) ===
    Route::put('/password-baru', [ProfileController::class, 'updatePassword'])->name('profile.password.update');

    // Menu Admin Lama (Source & Category)
    Route::resource('sources', SourceController::class);
    Route::resource('categories', CategoryController::class);

    // === NEW: DATA MASTER ===
    Route::middleware(['role:admin'])->group(function () {
        
        // UNIT PELAPOR
        Route::get('/master/reporters', [MasterController::class, 'reporterIndex'])->name('master.reporters.index');
        Route::post('/master/reporters', [MasterController::class, 'reporterStore'])->name('master.reporters.store');
        Route::put('/master/reporters/{id}', [MasterController::class, 'reporterUpdate'])->name('master.reporters.update'); 
        Route::delete('/master/reporters/{id}', [MasterController::class, 'reporterDestroy'])->name('master.reporters.destroy');

        // UNIT TUJUAN
        Route::get('/master/units', [MasterController::class, 'unitIndex'])->name('master.units.index');
        Route::post('/master/units', [MasterController::class, 'unitStore'])->name('master.units.store');
        Route::put('/master/units/{id}', [MasterController::class, 'unitUpdate'])->name('master.units.update'); 
        Route::delete('/master/units/{id}', [MasterController::class, 'unitDestroy'])->name('master.units.destroy');

        // KEGAWATAN (GRADES)
        Route::get('/master/grades', [MasterController::class, 'gradeIndex'])->name('master.grades.index');
        Route::post('/master/grades', [MasterController::class, 'gradeStore'])->name('master.grades.store');
        Route::put('/master/grades/{id}', [MasterController::class, 'gradeUpdate'])->name('master.grades.update'); 
        Route::delete('/master/grades/{id}', [MasterController::class, 'gradeDestroy'])->name('master.grades.destroy');
    });

    // Export Data
    Route::get('export-excel', [ComplaintController::class, 'exportExcel'])->name('export.excel');
    Route::get('/export-pdf', [ComplaintController::class, 'exportPdf'])->name('export.pdf');

    // Route::resource('complaints', ComplaintController::class); 
    
    // Kelola User (Hanya Admin)
    Route::resource('users', UserController::class)->middleware('role:admin');

});

require __DIR__.'/auth.php';

// ISI DATA OTOMATIS (SETUP AWAL SAJA)
Route::get('/install-data', function () {
    $sources = ['SMS/WA/Telepon', 'Datang Sendiri', 'Surat/Fax', 'Email', 'Medsos - Instagram', 'Medsos - TikTok', 'Medsos - Facebook'];
    foreach ($sources as $s) {
        \App\Models\Source::firstOrCreate(['name' => $s]);
    }
    return "BERHASIL! Data master sudah diinstall.";
});