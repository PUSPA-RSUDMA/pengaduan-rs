@extends('layouts.admin')

@section('title', 'Data Permohonan Informasi')

@section('content')
<div class="row">
    <div class="col-12">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm rounded-4" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white p-4 border-bottom d-flex justify-content-between align-items-center">
                <h5 class="fw-bold text-primary mb-0"><i class="bi bi-envelope-paper me-2"></i>List Permohonan Informasi</h5>
                <button type="button" class="btn btn-primary fw-medium rounded-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambah">
                    <i class="bi bi-plus-lg me-1"></i> Tambah Data
                </button>
            </div>
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle small">
                        <thead class="table-dark">
                            <tr>
                                <th width="5%">No</th>
                                <th>Tanggal</th>
                                <th>Pemohon</th>
                                <th>Pasien</th>
                                <th>No. HP</th>
                                <th>Keperluan</th>
                                <th class="text-center">Lampiran</th>
                                <th width="15%" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($data as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $item->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="fw-bold">{{ $item->nama_pemohon }}</td>
                                    <td>{{ $item->nama_pasien }}</td>
                                    <td><a href="https://wa.me/{{ $item->no_hp }}" target="_blank" class="text-decoration-none text-success"><i class="bi bi-whatsapp"></i> {{ $item->no_hp }}</a></td>
                                    <td>{{ Str::limit($item->keperluan, 50) }}</td>
                                    <td class="text-center">
                                        @if($item->file_lampiran)
                                        <a href="{{ Storage::disk('google')->url($item->file_lampiran) }}" target="_blank" class="btn btn-sm btn-outline-info rounded-3">
                                            <i class="bi bi-cloud-arrow-down"></i> Lihat dari G-Drive
                                        </a>
                                        @else
                                            <span class="badge bg-secondary">Tidak ada</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('permohonan-informasi.edit', $item->id) }}" class="btn btn-sm btn-warning rounded-3 shadow-sm" title="Edit">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <form action="{{ route('permohonan-informasi.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini? File lampiran juga akan terhapus permanen.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger rounded-3 shadow-sm" title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted fst-italic py-4">Belum ada data permohonan informasi.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL TAMBAH DATA --}}
<div class="modal fade" id="modalTambah" tabindex="-1" aria-labelledby="modalTambahLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header bg-light border-bottom-0">
                <h5 class="modal-title fw-bold text-dark" id="modalTambahLabel">Tambah Permohonan Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('permohonan-informasi.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Nama Pemohon <span class="text-danger">*</span></label>
                            <input type="text" name="nama_pemohon" class="form-control" required placeholder="Masukkan nama pemohon">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Nama Pasien <span class="text-danger">*</span></label>
                            <input type="text" name="nama_pasien" class="form-control" required placeholder="Masukkan nama pasien">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-semibold small">No. HP / WhatsApp <span class="text-danger">*</span></label>
                            <input type="number" name="no_hp" class="form-control" required placeholder="Contoh: 628123456789">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-semibold small">Keperluan <span class="text-danger">*</span></label>
                            <textarea name="keperluan" class="form-control" rows="3" required placeholder="Jelaskan keperluan permohonan informasi..."></textarea>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-semibold small">Upload Berkas Lampiran <span class="text-danger">*</span></label>
                            <input type="file" name="file_lampiran" class="form-control" required accept=".pdf,.jpg,.jpeg,.png">
                            <div class="form-text small">Format: PDF, JPG, PNG. Maksimal ukuran: 2MB.</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 bg-light">
                    <button type="button" class="btn btn-secondary rounded-3 fw-medium" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-3 fw-medium"><i class="bi bi-save me-1"></i> Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection