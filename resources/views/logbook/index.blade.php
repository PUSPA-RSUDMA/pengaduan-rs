@extends('layouts.admin')
@section('title', 'Kalender Logbook')
@section('content')

<!-- Load CSS FullCalendar -->
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold m-0"><i class="bi bi-calendar-event text-primary me-2"></i>Kalender Agenda Logbook</h5>
        <div class="d-flex align-items-center">
            <span class="badge bg-danger me-2">Warna Merah = H-1 (Besok)</span>
            <span class="badge bg-primary">Warna Biru = Normal</span>
        </div>
    </div>
    
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <p class="text-muted small mb-4">
            <i class="bi bi-info-circle"></i> <b>Petunjuk:</b> Klik pada tanggal berapapun di kalender untuk <b>menambah agenda</b>. Klik pada nama agenda yang sudah ada untuk <b>mengedit atau menghapus</b>.
        </p>

        <!-- Tempat Kalender Muncul -->
        <div id='calendar'></div>
    </div>
</div>

{{-- 1. MODAL TAMBAH LOGBOOK --}}
<div class="modal fade" id="modalTambahLogbook" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('logbook.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold">Tambah Agenda Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Tanggal Pelaksanaan</label>
                    <input type="date" id="inputTanggal" name="tanggal_acara" class="form-control" required readonly>
                    <small class="text-muted">Tanggal terpilih dari kalender</small>
                </div>
                <div class="mb-3">
                    <label class="form-label">Judul Agenda / Kegiatan</label>
                    <input type="text" name="judul_acara" class="form-control" required placeholder="Contoh: Rapat Evaluasi Bulanan">
                </div>
                <div class="mb-3">
                    <label class="form-label">Deskripsi / Catatan</label>
                    <textarea name="deskripsi" class="form-control" rows="3" placeholder="Keterangan tambahan..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Agenda</button>
            </div>
        </form>
    </div>
</div>

{{-- 2. MODAL EDIT & HAPUS LOGBOOK --}}
<div class="modal fade" id="modalEditLogbook" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square"></i> Edit / Hapus Agenda</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            
            {{-- Form Edit --}}
            <div class="modal-body">
                <form id="formEditLogbook" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label">Tanggal Pelaksanaan</label>
                        <input type="date" id="editTanggal" name="tanggal_acara" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Judul Agenda / Kegiatan</label>
                        <input type="text" id="editJudul" name="judul_acara" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi / Catatan</label>
                        <textarea id="editDeskripsi" name="deskripsi" class="form-control" rows="3"></textarea>
                    </div>
                </form>
            </div>
            
            <div class="modal-footer d-flex justify-content-between">
                {{-- Form Delete (Terpisah agar tidak bentrok dengan form Edit) --}}
                <form id="formDeleteLogbook" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus agenda ini secara permanen?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger"><i class="bi bi-trash"></i> Hapus Agenda</button>
                </form>

                <div>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    {{-- Tombol submit untuk Form Edit --}}
                    <button type="submit" form="formEditLogbook" class="btn btn-warning fw-bold">Simpan Perubahan</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Script untuk Inisialisasi FullCalendar -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'id', // Bahasa Indonesia
            height: 650,
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,dayGridWeek,listWeek'
            },
            buttonText: {
                today: 'Hari Ini',
                month: 'Bulan',
                week: 'Minggu',
                list: 'Daftar'
            },
            // Mengambil data acara dari Controller yang sudah dilempar sebagai Array JSON
            events: {!! json_encode($events) !!},
            
            // JIKA TANGGAL KOSONG DI-KLIK
            dateClick: function(info) {
                // Isi input tanggal di modal tambah sesuai tanggal yang diklik
                document.getElementById('inputTanggal').value = info.dateStr;
                
                // Tampilkan Modal Tambah
                var modalTambah = new bootstrap.Modal(document.getElementById('modalTambahLogbook'));
                modalTambah.show();
            },

            // JIKA ACARA YANG SUDAH ADA DI-KLIK
            eventClick: function(info) {
                // Ambil ID acara dan data-datanya
                var id = info.event.id;
                var title = info.event.title;
                // FullCalendar menyimpan tanggal dengan format ISO, kita ambil bagian YYYY-MM-DD saja
                var start = info.event.startStr.split('T')[0];
                var description = info.event.extendedProps.description;

                // Masukkan data ke dalam input Modal Edit
                document.getElementById('editJudul').value = title;
                document.getElementById('editTanggal').value = start;
                document.getElementById('editDeskripsi').value = description ? description : '';

                // Ubah Action URL pada Form Edit & Form Hapus (Menyesuaikan ID data)
                var baseURL = "{{ url('logbook') }}";
                document.getElementById('formEditLogbook').action = baseURL + '/' + id;
                document.getElementById('formDeleteLogbook').action = baseURL + '/' + id;

                // Tampilkan Modal Edit
                var modalEdit = new bootstrap.Modal(document.getElementById('modalEditLogbook'));
                modalEdit.show();
            }
        });

        calendar.render();
    });
</script>

<style>
    /* Sedikit perbaikan tampilan kalender agar kursor berubah menjadi tangan saat diarahkan ke tanggal */
    .fc-daygrid-day { cursor: pointer; }
    .fc-daygrid-day:hover { background-color: #f8f9fa; }
    .fc-event { cursor: pointer; }
</style>

@endsection