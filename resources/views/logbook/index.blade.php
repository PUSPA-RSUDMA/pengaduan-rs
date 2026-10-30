@extends('layouts.admin')
@section('title', 'Kalender Logbook')
@section('content')

<!-- Load CSS FullCalendar -->
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>

<style>
    /* Global Styles */
    body {
        background-color: #f4f7f6;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    }
    
    .card { 
        border-radius: 16px !important; 
        box-shadow: 0 4px 20px rgba(0,0,0,0.03) !important; 
        border: 1px solid rgba(0,0,0,0.02) !important;
        background-color: #ffffff;
    }

    .text-title { color: #111827; font-weight: 700; font-size: 1.15rem; }
    
    /* Modern Button Styling */
    .btn-modern-primary {
        background-color: #10b981;
        color: white; border: none; border-radius: 8px;
        font-weight: 600; transition: all 0.2s;
    }
    .btn-modern-primary:hover { background-color: #059669; color: white; transform: translateY(-1px); }

    /* Soft Badges */
    .badge-soft { border-radius: 20px; padding: 0.35rem 0.8rem; font-weight: 600; font-size: 0.75rem; }
    .badge-soft-danger { background-color: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
    .badge-soft-primary { background-color: #dbeafe; color: #1e40af; border: 1px solid #bfdbfe; }

    /* Form & Modal Styling */
    .form-control-modern, .form-select-modern {
        border-radius: 8px; border: 1px solid #e5e7eb; font-size: 0.85rem; padding: 0.5rem 0.75rem;
    }
    .form-control-modern:focus, .form-select-modern:focus {
        border-color: #10b981; box-shadow: 0 0 0 3px rgba(16,185,129,0.1);
    }
    .modal-content { border-radius: 16px; border: none; overflow: hidden; }
    .modal-header-modern { border-bottom: 1px solid #f3f4f6; padding: 1.25rem 1.5rem; background: white; }
    .modal-title-modern { font-weight: 700; color: #111827; font-size: 1.1rem; }
    .modal-footer-modern { border-top: 1px solid #f3f4f6; background: #f9fafb; padding: 1rem 1.5rem; }

    /* FullCalendar Custom Enhancements */
    .fc-daygrid-day { cursor: pointer; transition: 0.2s; }
    .fc-daygrid-day:hover { background-color: #f8fafc; }
    .fc-event { cursor: pointer; border-radius: 6px; padding: 3px 6px; border: none !important; font-size: 0.8rem; }
    .fc-toolbar-title { font-size: 1.25rem !important; font-weight: 700; color: #111827; }
    .fc-button {
        background-color: #f3f4f6 !important; border: 1px solid #e5e7eb !important; color: #374151 !important;
        font-weight: 600 !important; border-radius: 8px !important; padding: 0.4rem 0.8rem !important;
        box-shadow: none !important; text-transform: capitalize; transition: 0.2s;
    }
    .fc-button-active, .fc-button:hover {
        background-color: #10b981 !important; border-color: #10b981 !important; color: white !important;
    }

    /* Responsive Adjustments */
    @media (max-width: 768px) {
        .fc-toolbar { flex-direction: column; gap: 12px; }
        .fc-toolbar-title { font-size: 1.1rem !important; text-align: center; }
        .fc-toolbar-chunk { display: flex; justify-content: center; width: 100%; flex-wrap: wrap; gap: 5px; }
        .fc-button { padding: 0.3rem 0.6rem !important; font-size: 0.8rem !important; }
        .modal-body { padding: 1.25rem !important; }
    }
</style>

<div class="card border-0 mb-4">
    <div class="card-body p-3 p-md-4">
        
        {{-- HEADER SECTION --}}
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <div>
                <h5 class="text-title m-0"><i class="bi bi-calendar-event text-primary me-2"></i>Kalender Agenda Logbook</h5>
                <small class="text-muted">Kelola dan pantau jadwal kegiatan serta agenda harian</small>
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <span class="badge-soft badge-soft-danger"><i class="bi bi-circle-fill me-1" style="font-size: 6px;"></i> H-1 (Besok)</span>
                <span class="badge-soft badge-soft-primary"><i class="bi bi-circle-fill me-1" style="font-size: 6px;"></i> Normal</span>
            </div>
        </div>

        {{-- ALERTS --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" style="border-radius: 12px; background-color: #d1fae5; color: #065f46;">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="p-3 mb-4 rounded-3 bg-light border border-light" style="font-size: 0.85rem; color: #4b5563;">
            <i class="bi bi-info-circle-fill text-primary me-1"></i> <b>Petunjuk:</b> Klik pada tanggal di kalender untuk <b>menambah agenda</b>. Klik pada agenda untuk <b>melihat detail, mengedit, atau menghapus</b>.
        </div>

        <!-- Tempat Kalender Muncul -->
        <div id='calendar'></div>
    </div>
</div>

{{-- 1. MODAL TAMBAH LOGBOOK --}}
<div class="modal fade" id="modalTambahLogbook" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('logbook.store') }}" method="POST" class="modal-content shadow-lg">
            @csrf
            <div class="modal-header-modern d-flex justify-content-between align-items-center">
                <h6 class="modal-title-modern m-0"><i class="bi bi-journal-plus text-primary me-2"></i>Tambah Agenda Baru</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 bg-light">
                <div class="card border-0 shadow-sm p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted small mb-1">Tanggal Pelaksanaan</label>
                        <input type="date" id="inputTanggal" name="tanggal_acara" class="form-control form-control-modern bg-white" required readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted small mb-1">Judul Agenda / Kegiatan</label>
                        <input type="text" name="judul_acara" class="form-control form-control-modern" required placeholder="Contoh: Rapat Evaluasi Bulanan">
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-bold text-muted small mb-1">Deskripsi / Catatan</label>
                        <textarea name="deskripsi" class="form-control form-control-modern" rows="4" placeholder="Keterangan tambahan..."></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer-modern p-3 d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-light shadow-sm btn-modern px-3" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-modern-primary px-4">Simpan Agenda</button>
            </div>
        </form>
    </div>
</div>

{{-- 2. MODAL DETAIL, EDIT & HAPUS LOGBOOK --}}
<div class="modal fade" id="modalDetailLogbook" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg">
            <div class="modal-header-modern d-flex justify-content-between align-items-center">
                <h6 class="modal-title-modern m-0" id="modalDetailTitle"><i class="bi bi-card-text text-info me-2"></i>Rincian Agenda</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            
            <div class="modal-body p-4 bg-light">
                {{-- TAMPILAN VIEW DETAIL --}}
                <div id="viewModeWrapper">
                    <h5 id="viewJudul" class="fw-bold text-dark mb-2"></h5>
                    <div class="mb-3">
                        <span id="viewTanggal" class="badge-soft badge-soft-primary"><i class="bi bi-calendar me-1"></i> </span>
                    </div>
                    <div class="card border-0 shadow-sm p-3 bg-white">
                        <p class="fw-bold text-muted small border-bottom pb-2 mb-2">Deskripsi Kegiatan:</p>
                        <div id="viewDeskripsi" class="text-secondary" style="font-size: 0.9rem; white-space: pre-wrap; min-height: 80px;"></div>
                    </div>
                </div>

                {{-- TAMPILAN EDIT FORM (Sembunyi by default) --}}
                <div id="editModeWrapper" class="d-none">
                    <div class="card border-0 shadow-sm p-4">
                        <form id="formEditLogbook" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="mb-3">
                                <label class="form-label fw-bold text-muted small mb-1">Tanggal Pelaksanaan</label>
                                <input type="date" id="editTanggal" name="tanggal_acara" class="form-control form-control-modern" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold text-muted small mb-1">Judul Agenda / Kegiatan</label>
                                <input type="text" id="editJudul" name="judul_acara" class="form-control form-control-modern" required>
                            </div>
                            <div class="mb-0">
                                <label class="form-label fw-bold text-muted small mb-1">Deskripsi / Catatan</label>
                                <textarea id="editDeskripsi" name="deskripsi" class="form-control form-control-modern" rows="4"></textarea>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="modal-footer-modern p-3 d-flex justify-content-between align-items-center">
                {{-- Form Delete (Selalu tampil di kiri) --}}
                <form id="formDeleteLogbook" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus agenda ini secara permanen?');" class="m-0">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger px-3 py-2 fw-bold" style="border-radius: 8px;"><i class="bi bi-trash-fill me-1"></i> Hapus</button>
                </form>

                {{-- Tombol View Mode --}}
                <div id="btnViewMode" class="d-flex gap-2">
                    <button type="button" class="btn btn-light shadow-sm btn-modern px-3 border" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-warning text-dark fw-bold px-4 shadow-sm" style="border-radius: 8px;" onclick="toggleEditMode(true)">
                        <i class="bi bi-pencil-square me-1"></i> Edit
                    </button>
                </div>

                {{-- Tombol Edit Mode --}}
                <div id="btnEditMode" class="d-flex gap-2 d-none">
                    <button type="button" class="btn btn-light shadow-sm btn-modern px-3 border" onclick="toggleEditMode(false)">Batal</button>
                    <button type="submit" form="formEditLogbook" class="btn btn-modern-primary px-4 shadow-sm">Simpan</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Script untuk Inisialisasi FullCalendar -->
<script>
    function toggleEditMode(isEdit) {
        document.getElementById('viewModeWrapper').classList.toggle('d-none', isEdit);
        document.getElementById('btnViewMode').classList.toggle('d-none', isEdit);
        
        document.getElementById('editModeWrapper').classList.toggle('d-none', !isEdit);
        document.getElementById('btnEditMode').classList.toggle('d-none', !isEdit);

        document.getElementById('modalDetailTitle').innerHTML = isEdit 
            ? '<i class="bi bi-pencil-square text-warning me-2"></i>Edit Agenda' 
            : '<i class="bi bi-card-text text-info me-2"></i>Rincian Agenda';
    }

    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        var isMobile = window.innerWidth < 768;
        
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: isMobile ? 'listMonth' : 'dayGridMonth',
            locale: 'id',
            height: isMobile ? 'auto' : 700,
            contentHeight: 'auto',
            dayMaxEvents: true, // Memunculkan "more" jika agenda di satu hari terlalu banyak
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,listMonth'
            },
            buttonText: {
                today: 'Hari Ini',
                month: 'Bulan',
                list: 'Daftar'
            },
            events: {!! json_encode($events) !!},
            
            // KLIK TANGGAL KOSONG -> TAMBAH AGENDA
            dateClick: function(info) {
                document.getElementById('inputTanggal').value = info.dateStr;
                var modalTambah = new bootstrap.Modal(document.getElementById('modalTambahLogbook'));
                modalTambah.show();
            },

            // KLIK AGENDA -> LIHAT DETAIL / EDIT
            eventClick: function(info) {
                var id = info.event.id;
                var title = info.event.title;
                var start = info.event.startStr.split('T')[0];
                var description = info.event.extendedProps.description;

                // Format Tanggal untuk View Mode
                var dateObj = new Date(start);
                var formattedDate = dateObj.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });

                // Isi View Mode
                document.getElementById('viewJudul').innerText = title;
                document.getElementById('viewTanggal').innerHTML = '<i class="bi bi-calendar-event me-1"></i> ' + formattedDate;
                document.getElementById('viewDeskripsi').innerText = description ? description : 'Tidak ada catatan/deskripsi tambahan.';

                // Isi Edit Mode
                document.getElementById('editJudul').value = title;
                document.getElementById('editTanggal').value = start;
                document.getElementById('editDeskripsi').value = description ? description : '';

                // Atur Action Form Update & Delete
                var baseURL = "{{ url('logbook') }}";
                document.getElementById('formEditLogbook').action = baseURL + '/' + id;
                document.getElementById('formDeleteLogbook').action = baseURL + '/' + id;

                // Reset modal ke View Mode lalu tampilkan
                toggleEditMode(false);
                var modalDetail = new bootstrap.Modal(document.getElementById('modalDetailLogbook'));
                modalDetail.show();
            }
        });

        calendar.render();
    });
</script>

@endsection