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
use App\Http\Controllers\PermintaanController;
use App\Http\Controllers\KategoriPermintaanController;
use App\Http\Controllers\DashboardPermintaanController;
use App\Http\Controllers\LasehatController;
use App\Http\Controllers\LogbookController;
use App\Http\Controllers\WhatsappController;
use App\Http\Controllers\MainDashboardController;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Web Routes (AREA BEBAS LOGIN / PUBLIK)
|--------------------------------------------------------------------------
| Semua route yang ada di atas "middleware('auth')" bisa diakses 
| tanpa harus login terlebih dahulu.
*/

Route::get('/', function () {
    return redirect()->route('login');
});

// 1. Jalur Cronjob WA (WAJIB di luar auth agar robot cron-job.org bisa akses)
Route::get('/cron/send-reminder/{token}', function ($token) {
    if ($token !== 'rahasia-lasehat-123') {
        abort(403, 'Akses Ditolak!');
    }
    Artisan::call('logbook:send-reminder');
    return "Sukses mengeksekusi pengingat H-1: " . Artisan::output();
});

// 2. Logbook View (Bisa dilihat siapa saja tanpa login)
Route::get('/logbook', [LogbookController::class, 'index'])->name('logbook.index');

// 3. Area Complaints Umum
Route::resource('complaints', ComplaintController::class);
Route::get('complaints/{id}/print', [ComplaintController::class, 'print'])->name('complaints.print');
Route::post('complaints/import', [ComplaintController::class, 'import'])->name('complaints.import');
Route::get('complaints/download-template', [ComplaintController::class, 'downloadTemplate'])->name('complaints.template');


/*
|--------------------------------------------------------------------------
| AREA WAJIB LOGIN (AUTH)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    
    Route::get('/dashboard-utama', [MainDashboardController::class, 'index'])->middleware(['verified'])->name('utama.dashboard');

    // --- UBAH NAMA ROUTE PENGADUAN (Jika diperlukan) ---
    // (Route yang lama tetap ada, ini adalah Dashboard khusus Pengaduan)
    Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['verified'])->name('dashboard');

    // === AKSI LOGBOOK (Tambah & Hapus Wajib Login agar aman) ===
    // === AKSI LOGBOOK (Tambah, Edit, & Hapus Wajib Login) ===
    Route::post('/logbook', [LogbookController::class, 'store'])->name('logbook.store');
    Route::put('/logbook/{id}', [LogbookController::class, 'update'])->name('logbook.update'); // <--- TAMBAHAN BARU
    Route::delete('/logbook/{id}', [LogbookController::class, 'destroy'])->name('logbook.destroy');
    // 1. Rute Dashboard Permintaan
    Route::get('/dashboard-permintaan', [DashboardPermintaanController::class, 'index'])->name('dashboard.permintaan');

    // 2. Rute CRUD Permintaan
    Route::get('/permintaan', [PermintaanController::class, 'index'])->name('permintaan.index');
    Route::post('/permintaan', [PermintaanController::class, 'store'])->name('permintaan.store');
    Route::put('/permintaan/{id}', [PermintaanController::class, 'update'])->name('permintaan.update');
    Route::delete('/permintaan/{id}', [PermintaanController::class, 'destroy'])->name('permintaan.destroy');
    Route::get('/permintaan/export/pdf', [PermintaanController::class, 'exportPdf'])->name('permintaan.export.pdf');
    Route::get('/permintaan/export/excel', [PermintaanController::class, 'exportExcel'])->name('permintaan.export.excel');

    // FITUR PROFIL (DATA DIRI)
    Route::get('/profil', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profil', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profil', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::put('/password-baru', [ProfileController::class, 'updatePassword'])->name('profile.password.update');

    // Menu Admin Lama (Source & Category)
    Route::resource('sources', SourceController::class);
    Route::resource('categories', CategoryController::class);

    // Export Data Complaints
    Route::get('export-excel', [ComplaintController::class, 'exportExcel'])->name('export.excel');
    Route::get('/export-pdf', [ComplaintController::class, 'exportPdf'])->name('export.pdf');


    // === AREA KHUSUS ADMIN (ROLE: ADMIN) ===
    Route::middleware(['role:admin'])->group(function () {
        
        // Pengaturan & Test WA
        Route::get('/whatsapp', [WhatsappController::class, 'index'])->name('whatsapp.index');
        Route::post('/whatsapp/test', [WhatsappController::class, 'testSend'])->name('whatsapp.test');
        Route::get('/whatsapp/status', [WhatsappController::class, 'checkStatus'])->name('whatsapp.status');

        // Kelola User
        Route::resource('users', UserController::class);

        // DATA MASTER
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

        // Kategori Keluhan
        Route::resource('master/kategori-keluhan', App\Http\Controllers\KategoriKeluhanController::class);
        Route::post('master/kategori-keluhan/items', [App\Http\Controllers\KategoriKeluhanController::class, 'storeItem'])->name('kategori-keluhan.items.store');
        Route::delete('master/kategori-keluhan/items/{id}', [App\Http\Controllers\KategoriKeluhanController::class, 'destroyItem'])->name('kategori-keluhan.items.destroy');
        Route::put('kategori-keluhan/items/{id}', [App\Http\Controllers\KategoriKeluhanController::class, 'updateItem'])->name('kategori-keluhan.items.update');

        // Master Kategori Permintaan/Informasi
        Route::resource('kategori-permintaan', KategoriPermintaanController::class)->except(['create', 'show', 'edit']);
        Route::post('kategori-permintaan/items', [KategoriPermintaanController::class, 'storeItem'])->name('kategori-permintaan.items.store');
        Route::delete('kategori-permintaan/items/{id}', [KategoriPermintaanController::class, 'destroyItem'])->name('kategori-permintaan.items.destroy');
        Route::put('kategori-permintaan/items/{id}', [KategoriPermintaanController::class, 'updateItem'])->name('kategori-permintaan.items.update');

        // Master Supir
        Route::get('/master/supir', [MasterController::class, 'supirIndex'])->name('master.supir.index');
        Route::post('/master/supir', [MasterController::class, 'supirStore'])->name('master.supir.store');
        Route::delete('/master/supir/{id}', [MasterController::class, 'supirDestroy'])->name('master.supir.destroy');

        // LASEHAT GROUP
        Route::prefix('lasehat')->group(function () {
            Route::get('/dashboard', [LasehatController::class, 'dashboard'])->name('lasehat.dashboard');
            Route::get('/data', [LasehatController::class, 'index'])->name('lasehat.index');
            Route::post('/store', [LasehatController::class, 'store'])->name('lasehat.store');
            Route::post('/update-supir/{id}', [LasehatController::class, 'updateSupir'])->name('lasehat.update_supir');
            Route::get('/sync-spreadsheet', [LasehatController::class, 'syncGoogleSheet'])->name('lasehat.sync');
            Route::delete('/destroy/{id}', [LasehatController::class, 'destroy'])->name('lasehat.destroy');
        });
    });
});

require __DIR__.'/auth.php';

// ISI DATA OTOMATIS (SETUP AWAL SAJA) - Bebas Login
Route::get('/install-data', function () {
    $sources = ['SMS/WA/Telepon', 'Datang Sendiri', 'Surat/Fax', 'Email', 'Medsos - Instagram', 'Medsos - TikTok', 'Medsos - Facebook'];
    foreach ($sources as $s) {
        \App\Models\Source::firstOrCreate(['name' => $s]);
    }
    return "BERHASIL! Data master sudah diinstall.";
});