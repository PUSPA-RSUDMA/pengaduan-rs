@extends('layouts.admin')
@section('title', 'Data LaSehat')
@section('content')

<style>
    /* Global Styles matching the modern dashboard reference */
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
        background-color: #10b981; /* Emerald green */
        color: white; border: none; border-radius: 8px;
        font-weight: 600; transition: all 0.2s;
    }
    .btn-modern-primary:hover { background-color: #059669; color: white; transform: translateY(-1px); }

    .btn-action-icon {
        width: 32px; height: 32px;
        display: inline-flex; align-items: center; justify-content: center;
        border-radius: 8px; border: none; transition: 0.2s;
    }
    .btn-action-icon:hover { transform: scale(1.05); }

    /* Modern Table Styling */
    .table-modern { margin-bottom: 0; }
    .table-modern > :not(caption) > * > * {
        padding: 1rem 1rem; border-bottom-color: #f3f4f6;
        vertical-align: middle; background-color: transparent !important;
    }
    .table-modern thead th {
        text-transform: uppercase; font-size: 0.75rem; font-weight: 600;
        color: #6b7280; border-bottom: 2px solid #f3f4f6 !important; letter-spacing: 0.5px;
    }
    .table-modern tbody tr:hover { background-color: #f8fafc !important; }

    /* Soft Badges */
    .badge-soft { border-radius: 20px; padding: 0.35rem 0.8rem; font-weight: 600; font-size: 0.75rem; }
    .badge-soft-success { background-color: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
    .badge-soft-info { background-color: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; }
    .badge-soft-warning { background-color: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
    .badge-soft-secondary { background-color: #f3f4f6; color: #374151; border: 1px solid #e5e7eb; }

    /* Form Styling */
    .form-control-modern, .form-select-modern {
        border-radius: 8px; border: 1px solid #e5e7eb; font-size: 0.85rem; padding: 0.5rem 0.75rem;
    }
    .form-control-modern:focus, .form-select-modern:focus {
        border-color: #10b981; box-shadow: 0 0 0 3px rgba(16,185,129,0.1);
    }
    .filter-wrapper {
        background-color: #f8fafc; border-radius: 12px; border: 1px solid #f3f4f6;
    }

    /* Modal Styling */
    .modal-content { border-radius: 16px; border: none; }
    .modal-header-modern { border-bottom: 1px solid #f3f4f6; padding: 1.25rem 1.5rem; background: white; border-radius: 16px 16px 0 0; }
    .modal-title-modern { font-weight: 700; color: #111827; font-size: 1.1rem; }
    .modal-footer-modern { border-top: 1px solid #f3f4f6; background: #f9fafb; border-radius: 0 0 16px 16px; }
</style>

<div class="card border-0 mb-4">
    <div class="card-body p-4">
        
        {{-- HEADER SECTION --}}
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <div>
                <h5 class="text-title m-0"><i class="bi bi-list-ul text-danger me-2"></i>Daftar Pengantaran Pasien (LaSehat)</h5>
                <small class="text-muted">Kelola data pengantaran pasien ambulans dan sinkronisasi spreadsheet</small>
            </div>
            <div class="d-flex gap-2">
                {{-- Tombol Sync Google Sheets --}}
                <a href="{{ route('lasehat.sync') }}" class="btn btn-sm px-3 py-2 fw-bold text-success bg-success-subtle border-success-subtle shadow-sm rounded-pill d-flex align-items-center">
                    <i class="bi bi-google me-1"></i> Sync Spreadsheet
                </a>
                {{-- Tombol Tambah Manual --}}
                <button type="button" class="btn-modern-primary btn-sm px-3 py-2 shadow-sm d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#modalTambah">
                    <i class="bi bi-plus-circle me-1"></i> Tambah Manual
                </button>
            </div>
        </div>
        
        {{-- ALERTS --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" style="border-radius: 12px; background-color: #d1fae5; color: #065f46;">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" style="border-radius: 12px; background-color: #fee2e2; color: #991b1b;">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- FORM FILTER & SORTING --}}
        <form method="GET" action="{{ route('lasehat.index') }}" class="row g-3 mb-4 align-items-end filter-wrapper p-3 shadow-sm">
            <div class="col-md-2">
                <label class="form-label small fw-bold text-muted mb-1">Dari Tanggal</label>
                <input type="date" name="start_date" class="form-control form-control-modern" value="{{ request('start_date') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-bold text-muted mb-1">Sampai Tanggal</label>
                <input type="date" name="end_date" class="form-control form-control-modern" value="{{ request('end_date') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-bold text-muted mb-1">Status Supir</label>
                <select name="status_supir" class="form-select form-select-modern">
                    <option value="">-- Semua --</option>
                    <option value="sudah" {{ request('status_supir') == 'sudah' ? 'selected' : '' }}>Sudah Ada</option>
                    <option value="belum" {{ request('status_supir') == 'belum' ? 'selected' : '' }}>Belum Ada</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-bold text-muted mb-1">Urutkan Tanggal</label>
                <select name="sort_date" class="form-select form-select-modern">
                    <option value="desc" {{ request('sort_date', 'desc') == 'desc' ? 'selected' : '' }}>Terbaru (Baru ke Lama)</option>
                    <option value="asc" {{ request('sort_date') == 'asc' ? 'selected' : '' }}>Terlama (Lama ke Baru)</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-modern-primary btn-sm fw-bold w-100 py-2">
                    <i class="bi bi-filter me-1"></i> Terapkan
                </button>
                <a href="{{ route('lasehat.index') }}" class="btn btn-light border btn-sm fw-bold px-3 d-flex align-items-center" title="Reset Filter">
                    <i class="bi bi-arrow-counterclockwise"></i>
                </a>
            </div>
        </form>
        {{-- END FORM FILTER --}}

        <div class="table-responsive border rounded-3" style="border-color: #f3f4f6 !important;">
            <table class="table table-modern align-middle table-hover">
                <thead class="bg-light text-center">
                    <tr>
                        <th>Tgl Pengantaran</th>
                        <th>Nama Pasien</th>
                        <th>Ruangan</th>
                        <th>Alamat Tujuan</th>
                        <th>Penanggung Jawab</th>
                        <th>Sumber</th>
                        <th>Supir Ambulance</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-center">
                    @forelse($lasehats as $item)
                    <tr>
                        <td>
                            <div class="fw-bold text-dark">{{ \Carbon\Carbon::parse($item->tanggal_pengantaran)->format('d/m/Y') }}</div>
                        </td>
                        <td><span class="fw-bold text-dark">{{ $item->nama_pasien }}</span></td>
                        <td><span class="badge-soft badge-soft-secondary">{{ $item->tempat_dirawat }}</span></td>
                        <td class="text-start"><span class="text-muted" style="font-size: 0.85rem;">{{ Str::limit($item->alamat_tujuan, 30) }}</span></td>
                        <td class="text-start">
                            <div class="fw-bold text-dark" style="font-size: 0.85rem;">{{ $item->penanggung_jawab }}</div>
                            <small class="text-muted">{{ $item->no_telp_pj }}</small>
                        </td>
                        <td>
                            @if($item->created_by == 'Google Form')
                                <span class="badge-soft badge-soft-success"><i class="bi bi-google me-1"></i> Spreadsheet</span>
                            @else
                                <span class="badge-soft badge-soft-info">Manual System</span>
                            @endif
                        </td>
                        <td>
                            @if($item->supir_ambulance)
                                <div class="d-flex flex-column align-items-center gap-1">
                                    <span class="fw-bold text-success small"><i class="bi bi-check-circle-fill me-1"></i> {{ $item->supir_ambulance }}</span>
                                    <button class="btn btn-sm btn-link py-0 text-decoration-none" style="font-size: 0.75rem;" data-bs-toggle="modal" data-bs-target="#modalSupir{{ $item->id }}">Ubah Supir</button>
                                </div>
                            @else
                                <button class="btn btn-warning btn-sm fw-bold px-3 py-1 shadow-sm" style="font-size: 0.75rem; border-radius: 20px;" data-bs-toggle="modal" data-bs-target="#modalSupir{{ $item->id }}">
                                    <i class="bi bi-person-plus me-1"></i> Isi Supir
                                </button>
                            @endif
                        </td>
                        <td>
                            <form action="{{ route('lasehat.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus data pasien {{ $item->nama_pasien }}?');" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-action-icon bg-danger-subtle text-danger shadow-sm" title="Hapus Data">
                                    <i class="bi bi-trash-fill"></i>
                                </button>
                            </form>
                        </td>
                    </tr>

                    {{-- Modal Isi Supir --}}
                    <div class="modal fade" id="modalSupir{{ $item->id }}" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content shadow-lg">
                                <form action="{{ route('lasehat.update_supir', $item->id) }}" method="POST">
                                    @csrf
                                    <div class="modal-header-modern d-flex justify-content-between align-items-center">
                                        <h6 class="modal-title-modern m-0"><i class="bi bi-person-badge text-warning me-2"></i>Pilih Supir Ambulance</h6>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body p-4 bg-light">
                                        <div class="card border-0 shadow-sm p-3">
                                            <label class="form-label fw-bold text-muted small mb-2">Nama Supir Ambulance</label>
                                            <select name="supir_ambulance" class="form-select form-select-modern" required>
                                                <option value="" disabled selected>-- Pilih Supir --</option>
                                                @foreach($supirs as $s)
                                                    <option value="{{ $s->nama_supir }}" {{ $item->supir_ambulance == $s->nama_supir ? 'selected' : '' }}>
                                                        {{ $s->nama_supir }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="modal-footer-modern p-3 d-flex justify-content-end gap-2">
                                        <button type="button" class="btn btn-light shadow-sm btn-modern px-3" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-warning text-dark fw-bold btn-modern shadow-sm px-4">Simpan Supir</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <div class="text-muted d-flex flex-column align-items-center">
                                <i class="bi bi-folder2-open fs-1 text-light mb-2"></i>
                                <span>Belum ada data pengantaran pasien.</span>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4 d-flex justify-content-end">
            {{ $lasehats->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>

{{-- Modal Tambah Manual --}}
<div class="modal fade" id="modalTambah" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form action="{{ route('lasehat.store') }}" method="POST" class="modal-content shadow-lg">
            @csrf
            <div class="modal-header-modern d-flex justify-content-between align-items-center">
                <h6 class="modal-title-modern m-0"><i class="bi bi-journal-plus text-primary me-2"></i>Tambah Data LaSehat (Manual)</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 bg-light">
                <div class="card border-0 shadow-sm p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-muted small mb-1">Nama Pasien</label>
                            <input type="text" name="nama_pasien" class="form-control form-control-modern" required placeholder="Masukkan nama pasien">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-muted small mb-1">Tempat Dirawat (Ruangan)</label>
                            <select name="tempat_dirawat" class="form-select form-select-modern" required>
                                <option value="">-- Pilih Ruangan --</option>
                                @foreach($ruangans as $r)
                                    <option value="{{ $r }}">{{ $r }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-muted small mb-1">Tanggal Pengantaran</label>
                            <input type="date" name="tanggal_pengantaran" class="form-control form-control-modern" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-muted small mb-1">Supir Ambulance (Boleh Kosong)</label>
                            <select name="supir_ambulance" class="form-select form-select-modern">
                                <option value="">-- Nanti Saja / Belum Ada --</option>
                                @foreach($supirs as $s)
                                    <option value="{{ $s->nama_supir }}">{{ $s->nama_supir }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-muted small mb-1">Nama Penanggung Jawab</label>
                            <input type="text" name="penanggung_jawab" class="form-control form-control-modern" required placeholder="Nama PJ">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-muted small mb-1">No. Telepon / WA PJ</label>
                            <input type="text" name="no_telp_pj" class="form-control form-control-modern" required placeholder="Contoh: 08123456789">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold text-muted small mb-1">Alamat Tujuan Pengantaran</label>
                            <textarea name="alamat_tujuan" class="form-control form-control-modern" rows="3" required placeholder="Tulis alamat lengkap tujuan..."></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer-modern p-3 d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-light shadow-sm btn-modern px-4" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-modern-primary px-4">Simpan Data</button>
            </div>
        </form>
    </div>
</div>
@endsection