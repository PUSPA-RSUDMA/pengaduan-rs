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
use App\Http\Controllers\WhatsappInboxController;
use App\Http\Controllers\BpjsController;
use App\Http\Controllers\PermohonanInformasiController;
use App\Http\Controllers\PermohonanUhcController;
use App\Http\Controllers\MasterRequestPkrsController;
use App\Http\Controllers\MasterOutputPkrsController;
use App\Http\Controllers\PkrsController;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Web Routes (AREA BEBAS LOGIN / PUBLIK)
|--------------------------------------------------------------------------
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

// 2. Logbook View (Bisa dilihat siapa saja tanpa login jika publik, atau dengan hak akses)
Route::get('/logbook', [LogbookController::class, 'index'])->name('logbook.index');


/*
|--------------------------------------------------------------------------
| AREA WAJIB LOGIN (AUTH) & HAK AKSES DINAMIS PER MENU/ACTION
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    
    // --- HOME DASHBOARD UTAMA ---
    Route::get('/dashboard-utama', [MainDashboardController::class, 'index'])
        ->middleware(['verified', 'permission:utama_dashboard,view'])
        ->name('utama.dashboard');

    // --- PENGADUAN ---
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware(['verified', 'permission:pengaduan_dashboard,view'])
        ->name('dashboard');

    Route::middleware(['permission:complaints'])->group(function () {
        Route::resource('complaints', ComplaintController::class);
        Route::get('complaints/{id}/print', [ComplaintController::class, 'print'])->name('complaints.print');
        Route::post('complaints/import', [ComplaintController::class, 'import'])->name('complaints.import');
        Route::get('complaints/download-template', [ComplaintController::class, 'downloadTemplate'])->name('complaints.template');
        Route::get('export-excel', [ComplaintController::class, 'exportExcel'])->name('export.excel');
        Route::get('/export-pdf', [ComplaintController::class, 'exportPdf'])->name('export.pdf');
    });

    // --- LAYANAN INFORMASI & PERMINTAAN ---
    Route::get('/dashboard-permintaan', [DashboardPermintaanController::class, 'index'])
        ->middleware(['permission:permintaan_dashboard,view'])
        ->name('dashboard.permintaan');

    Route::middleware(['permission:permintaan'])->group(function () {
        Route::get('/permintaan', [PermintaanController::class, 'index'])->name('permintaan.index');
        Route::post('/permintaan', [PermintaanController::class, 'store'])->name('permintaan.store');
        Route::put('/permintaan/{id}', [PermintaanController::class, 'update'])->name('permintaan.update');
        Route::delete('/permintaan/{id}', [PermintaanController::class, 'destroy'])->name('permintaan.destroy');
        Route::get('/permintaan/export/pdf', [PermintaanController::class, 'exportPdf'])->name('permintaan.export.pdf');
        Route::get('/permintaan/export/excel', [PermintaanController::class, 'exportExcel'])->name('permintaan.export.excel');
    });

    // --- PERMOHONAN INFORMASI ---
    Route::middleware(['permission:permohonan_informasi'])->group(function () {
        Route::resource('permohonan-informasi', PermohonanInformasiController::class);
    });

    // --- PERALIHAN UHC ---
    Route::middleware(['permission:permohonan_uhc'])->group(function () {
        Route::resource('permohonan-uhc', PermohonanUhcController::class);
    });

    // --- PROMOSI KESEHATAN (PKRS) ---
    Route::get('/pkrs/dashboard', [PkrsController::class, 'dashboard'])
        ->middleware(['permission:pkrs_dashboard,view'])
        ->name('pkrs.dashboard');

    Route::middleware(['permission:pkrs'])->group(function () {
        Route::resource('pkrs', PkrsController::class)->except(['create', 'show', 'edit']);
    });

    // --- MOBIL LASEHAT ---
    Route::prefix('lasehat')->group(function () {
        Route::get('/dashboard', [LasehatController::class, 'dashboard'])
            ->middleware(['permission:lasehat_dashboard,view'])
            ->name('lasehat.dashboard');

        Route::middleware(['permission:lasehat'])->group(function () {
            Route::get('/data', [LasehatController::class, 'index'])->name('lasehat.index');
            Route::post('/store', [LasehatController::class, 'store'])->name('lasehat.store');
            Route::post('/update-supir/{id}', [LasehatController::class, 'updateSupir'])
                ->middleware(['permission:lasehat,edit'])
                ->name('lasehat.update_supir');
            Route::get('/sync-spreadsheet', [LasehatController::class, 'syncGoogleSheet'])
                ->middleware(['permission:lasehat,create'])
                ->name('lasehat.sync');
            Route::delete('/destroy/{id}', [LasehatController::class, 'destroy'])->name('lasehat.destroy');
        });
    });

    // --- LOGBOOK ---
    Route::middleware(['permission:logbook'])->group(function () {
        Route::post('/logbook', [LogbookController::class, 'store'])->name('logbook.store');
        Route::put('/logbook/{id}', [LogbookController::class, 'update'])->name('logbook.update');
        Route::delete('/logbook/{id}', [LogbookController::class, 'destroy'])->name('logbook.destroy');
    });

    // --- WHATSAPP GATEWAY & LIVE CHAT ---
    Route::middleware(['permission:whatsapp'])->group(function () {
        Route::get('/whatsapp', [WhatsappController::class, 'index'])->name('whatsapp.index');
        Route::get('/whatsapp/status', [WhatsappController::class, 'checkStatus'])->name('whatsapp.status');
        Route::post('/whatsapp/test', [WhatsappController::class, 'testSend'])->name('whatsapp.test');
        Route::get('/whatsapp/api/chats', [WhatsappController::class, 'getChats'])->name('whatsapp.api.chats');
        Route::get('/whatsapp/api/messages', [WhatsappController::class, 'getMessages'])->name('whatsapp.api.messages');
        Route::delete('/whatsapp/api/chats', [WhatsappController::class, 'deleteChat'])->name('whatsapp.api.chats.delete');
        Route::post('/whatsapp/api/sync', [WhatsappController::class, 'syncChats'])
            ->middleware(['permission:whatsapp,view'])
            ->name('whatsapp.api.sync');
    });

    // --- BPJS KEPESERTAAN ---
    Route::middleware(['permission:bpjs'])->group(function () {
        Route::prefix('bpjs')->group(function () {
            Route::get('/peserta', [BpjsController::class, 'index'])->name('bpjs.index');
            Route::get('/peserta/cari', [BpjsController::class, 'cariPeserta'])->name('bpjs.cari');
        });
        Route::post('/bpjs/update-kontrol', [BpjsController::class, 'updateKontrolDate'])
            ->middleware(['permission:bpjs,edit'])
            ->name('bpjs.updateKontrol');
    });

    // --- DATA MASTER ---
    Route::middleware(['permission:master'])->group(function () {
        Route::resource('sources', SourceController::class);
        Route::resource('categories', CategoryController::class);

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

        // Master Data PKRS
        Route::resource('master/request-pkrs', MasterRequestPkrsController::class, [
            'as' => 'master'
        ]);
        Route::resource('master/output-pkrs', MasterOutputPkrsController::class, [
            'as' => 'master'
        ]);
    });

    // --- MANAGEMENT USER & HAK AKSES ---
    Route::middleware(['permission:users'])->group(function () {
        Route::resource('users', UserController::class);
    });

    // --- FITUR PROFIL (DATA DIRI - Semua user login) ---
    Route::get('/profil', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profil', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profil', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::put('/password-baru', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
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
