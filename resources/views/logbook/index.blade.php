@extends('layouts.admin')
@section('title', 'Logbook Agenda')
@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold m-0"><i class="bi bi-journal-text text-primary me-2"></i>Logbook Agenda & Kegiatan</h5>
        <button type="button" class="btn btn-primary btn-sm fw-bold" data-bs-toggle="modal" data-bs-target="#modalTambahLogbook">
            <i class="bi bi-plus-circle"></i> Tambah Agenda
        </button>
    </div>
    
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th width="15%">Tanggal Acara</th>
                        <th>Judul Agenda / Kegiatan</th>
                        <th>Deskripsi</th>
                        <th width="15%" class="text-center">Status H-1</th>
                        <th width="10%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logbooks as $item)
                    @php
                        $tglAcara = \Carbon\Carbon::parse($item->tanggal_acara);
                        $besok = \Carbon\Carbon::tomorrow();
                        $isH1 = $tglAcara->isSameDay($besok);
                    @endphp
                    <tr>
                        <td class="fw-bold text-primary">{{ $tglAcara->format('d/m/Y') }}</td>
                        <td>{{ $item->judul_acara }}</td>
                        <td>{{ $item->deskripsi ?? '-' }}</td>
                        <td class="text-center">
                            @if($isH1)
                                <span class="badge bg-danger animate-pulse">H-1 (Besok!)</span>
                            @else
                                <span class="badge bg-secondary">Normal</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <form action="{{ route('logbook.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus agenda ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-3">Belum ada agenda di logbook.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $logbooks->links() }}
    </div>
</div>

{{-- Modal Tambah Logbook --}}
<div class="modal fade" id="modalTambahLogbook" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('logbook.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold">Tambah Agenda Logbook</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Judul Agenda / Kegiatan</label>
                    <input type="text" name="judul_acara" class="form-control" required placeholder="Contoh: Rapat Evaluasi Bulanan">
                </div>
                <div class="mb-3">
                    <label class="form-label">Tanggal Pelaksanaan Acara</label>
                    <input type="date" name="tanggal_acara" class="form-control" required>
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
@endsection