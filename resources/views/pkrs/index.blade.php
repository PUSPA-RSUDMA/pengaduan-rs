@extends('layouts.admin')
@section('title', 'Data Kegiatan PKRS')
@section('content')

<style>
    body { background-color: #f4f7f6; font-family: 'Inter', sans-serif; }
    .card { border-radius: 16px !important; box-shadow: 0 4px 20px rgba(0,0,0,0.03) !important; border: 1px solid rgba(0,0,0,0.02) !important; background-color: #ffffff; }
    .text-title { color: #111827; font-weight: 700; font-size: 1.15rem; }
    .btn-modern-primary { background-color: #10b981; color: white; border: none; border-radius: 8px; padding: 0.5rem 1rem; font-weight: 600; transition: all 0.2s; }
    .btn-modern-primary:hover { background-color: #059669; color: white; transform: translateY(-1px); }
    .btn-action-icon { width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; border-radius: 8px; border: none; transition: 0.2s; }
    .btn-action-icon:hover { transform: scale(1.05); }
    .table-modern > :not(caption) > * > * { padding: 1rem; border-bottom-color: #f3f4f6; vertical-align: middle; background-color: transparent !important; }
    .table-modern thead th { text-transform: uppercase; font-size: 0.75rem; font-weight: 600; color: #6b7280; border-bottom: 2px solid #f3f4f6 !important; letter-spacing: 0.5px; }
    .badge-soft { border-radius: 20px; padding: 0.35rem 0.8rem; font-weight: 600; font-size: 0.75rem; }
    .form-control-modern, .form-select-modern { border-radius: 8px; border: 1px solid #e5e7eb; font-size: 0.85rem; padding: 0.5rem 0.75rem; }
    .form-control-modern:focus, .form-select-modern:focus { border-color: #10b981; box-shadow: 0 0 0 3px rgba(16,185,129,0.1); }
    .modal-content { border-radius: 16px; border: none; }
    .modal-header-modern { border-bottom: 1px solid #f3f4f6; padding: 1.25rem 1.5rem; background: white; border-radius: 16px 16px 0 0; }
    .modal-footer-modern { border-top: 1px solid #f3f4f6; background: #f9fafb; border-radius: 0 0 16px 16px; }
</style>

<div class="card border-0 mb-4">
    <div class="card-body p-4">
        {{-- HEADER --}}
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
            <div>
                <h5 class="text-title m-0">Data Kegiatan PKRS</h5>
                <small class="text-muted">Kelola jadwal dan laporan kegiatan Promosi Kesehatan RS</small>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('pkrs.export.excel', request()->only(['search', 'status', 'sort'])) }}" class="btn btn-outline-success fw-semibold shadow-sm" style="border-radius: 8px; padding: 0.5rem 1rem;">
                    <i class="bi bi-file-earmark-excel-fill me-1"></i> Export Excel
                </a>
                @if(auth()->user()->hasPermission('pkrs', 'create'))
                <button type="button" class="btn-modern-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#modalInput">   
                    <i class="bi bi-plus-lg me-1"></i> Tambah Kegiatan
                </button>
                @endif
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" style="border-radius: 12px; background-color: #d1fae5; color: #065f46;">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- FORM FILTER & SORTING --}}
        <div class="mb-4 p-3 bg-light rounded-3 border" style="border-color: #f3f4f6 !important;">
            <form action="{{ route('pkrs.index') }}" method="GET" class="row g-2 align-items-center">
                {{-- Input Pencarian --}}
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control form-control-modern" placeholder="Cari Tema / Kegiatan..." value="{{ request('search') }}">
                </div>
                
                {{-- Filter Status --}}
                <div class="col-md-3">
                    <select name="status" class="form-select form-control-modern" onchange="this.form.submit()">
                        <option value="">-- Semua Status --</option>
                        <option value="Belum" {{ request('status') == 'Belum' ? 'selected' : '' }}>Belum</option>
                        <option value="Proses" {{ request('status') == 'Proses' ? 'selected' : '' }}>Proses</option>
                        <option value="Selesai" {{ request('status') == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                    </select>
                </div>
                
                {{-- Filter Sorting (Terbaru/Terlama) --}}
                <div class="col-md-3">
                    <select name="sort" class="form-select form-control-modern" onchange="this.form.submit()">
                        <option value="desc" {{ request('sort', 'desc') == 'desc' ? 'selected' : '' }}>Urutkan: Tanggal Terbaru</option>
                        <option value="asc" {{ request('sort') == 'asc' ? 'selected' : '' }}>Urutkan: Tanggal Terlama</option>
                    </select>
                </div>
                
                {{-- Tombol Terapkan & Reset --}}
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-modern-primary w-100 py-2"><i class="bi bi-funnel"></i> Terapkan</button>
                    {{-- Tombol reset akan muncul jika ada filter aktif selain kondisi default --}}
                    @if(request()->anyFilled(['search', 'status']) || request('sort') === 'asc')
                        <a href="{{ route('pkrs.index') }}" class="btn btn-light border py-2 shadow-sm" title="Reset Filter"><i class="bi bi-arrow-clockwise"></i></a>
                    @endif
                </div>
            </form>
        </div>

        {{-- TABLE --}}
        <div class="table-responsive border rounded-3" style="border-color: #f3f4f6 !important;">
            <table class="table table-modern align-middle table-hover">
                <thead class="bg-light text-center">
                    <tr>
                        <th width="5%">No</th>
                        <th>Tanggal & Hari</th>
                        <th>Tema/Peringatan</th>
                        <th width="20%">Rencana Kegiatan</th>
                        <th>Request By</th>
                        <th>Output</th>
                        <th>Bukti</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-center">
                    @forelse($dataPkrs as $item)
                    <tr>
                        <td class="text-muted fw-bold">{{ $loop->iteration + $dataPkrs->firstItem() - 1 }}</td>
                        <td>
                            <div class="fw-bold text-dark">{{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}</div>
                            <small class="text-muted">{{ $item->hari }}</small>
                        </td>
                        <td class="fw-bold text-dark">{{ $item->tema }}</td>
                        <td class="text-start"><span class="text-muted" style="font-size: 0.85rem;">{{ $item->rencana_kegiatan }}</span></td>
                        <td><span class="badge bg-light text-dark border">{{ $item->request_by }}</span></td>
                        <td><span class="badge bg-light text-dark border">{{ $item->output }}</span></td>
                        <td>
                            @if($item->bukti)
                                <a href="{{ $item->bukti }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill" style="font-size: 0.7rem;"><i class="bi bi-link-45deg"></i> Link Bukti</a>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if($item->status == 'Selesai')
                                <span class="badge-soft" style="background-color: #d1fae5; color: #065f46;">Selesai</span>
                            @elseif($item->status == 'Proses')
                                <span class="badge-soft" style="background-color: #fef3c7; color: #92400e;">Proses</span>
                            @else
                                <span class="badge-soft" style="background-color: #fee2e2; color: #991b1b;">Belum</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex justify-content-center gap-1">
                                @if(auth()->user()->hasPermission('pkrs', 'edit'))
                                <button type="button" class="btn btn-action-icon bg-warning-subtle text-warning shadow-sm" data-bs-toggle="modal" data-bs-target="#modalEdit{{ $item->id }}">
                                    <i class="bi bi-pencil-fill"></i>
                                </button>
                                @endif
                                @if(auth()->user()->hasPermission('pkrs', 'delete'))
                                <form action="{{ route('pkrs.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus data ini?');" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-action-icon bg-danger-subtle text-danger shadow-sm"><i class="bi bi-trash-fill"></i></button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>

                    {{-- MODAL EDIT --}}
                    <div class="modal fade" id="modalEdit{{ $item->id }}" tabindex="-1">
                        <div class="modal-dialog modal-lg modal-dialog-centered">
                            <div class="modal-content shadow-lg">
                                <div class="modal-header-modern">
                                    <h6 class="modal-title-modern m-0"><i class="bi bi-pencil-square text-warning me-2"></i> Edit Data PKRS</h6>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <form action="{{ route('pkrs.update', $item->id) }}" method="POST">
                                    @csrf @method('PUT')
                                    <div class="modal-body p-4 bg-light">
                                        <div class="row g-3">
                                            <div class="col-md-4">
                                                <label class="small fw-bold text-muted mb-1">Tanggal</label>
                                                <input type="date" name="tanggal" class="form-control form-control-modern" value="{{ $item->tanggal }}" required>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="small fw-bold text-muted mb-1">Tema / Peringatan</label>
                                                <input type="text" name="tema" class="form-control form-control-modern" value="{{ $item->tema }}" required>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="small fw-bold text-muted mb-1">Status</label>
                                                <select name="status" class="form-select form-control-modern" required>
                                                    <option value="Belum" {{ $item->status == 'Belum' ? 'selected' : '' }}>Belum</option>
                                                    <option value="Proses" {{ $item->status == 'Proses' ? 'selected' : '' }}>Proses</option>
                                                    <option value="Selesai" {{ $item->status == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="small fw-bold text-muted mb-1">Request By</label>
                                                <select name="request_by" class="form-select form-control-modern" required>
                                                    @foreach($requests as $req)
                                                        <option value="{{ $req->name }}" {{ $item->request_by == $req->name ? 'selected' : '' }}>{{ $req->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="small fw-bold text-muted mb-1">Output</label>
                                                <select name="output" class="form-select form-control-modern" required>
                                                    @foreach($outputs as $out)
                                                        <option value="{{ $out->name }}" {{ $item->output == $out->name ? 'selected' : '' }}>{{ $out->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-12">
                                                <label class="small fw-bold text-muted mb-1">Rencana Kegiatan</label>
                                                <textarea name="rencana_kegiatan" class="form-control form-control-modern" rows="2" required>{{ $item->rencana_kegiatan }}</textarea>
                                            </div>
                                            <div class="col-12">
                                                <label class="small fw-bold text-muted mb-1">Bukti (Link Drive/Doc)</label>
                                                <input type="text" name="bukti" class="form-control form-control-modern" value="{{ $item->bukti }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer-modern p-3 d-flex justify-content-end gap-2">
                                        <button type="button" class="btn btn-light shadow-sm btn-modern" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-warning text-dark fw-bold btn-modern shadow-sm px-4">Update Data</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-5 text-muted">Belum ada data atau tidak ditemukan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $dataPkrs->links('pagination::bootstrap-5') }}</div>
    </div>
</div>

{{-- MODAL TAMBAH DATA --}}
<div class="modal fade" id="modalInput" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-lg">
            <div class="modal-header-modern">
                <h6 class="modal-title-modern m-0"><i class="bi bi-plus-circle text-primary me-2"></i> Tambah Kegiatan PKRS</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('pkrs.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4 bg-light">
                    <div class="alert alert-info py-2" style="font-size: 0.8rem;"><i class="bi bi-info-circle me-1"></i> Nama Hari akan terisi otomatis berdasarkan tanggal yang Anda pilih.</div>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="small fw-bold text-muted mb-1">Tanggal <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal" class="form-control form-control-modern" required>
                        </div>
                        <div class="col-md-4">
                            <label class="small fw-bold text-muted mb-1">Tema / Peringatan <span class="text-danger">*</span></label>
                            <input type="text" name="tema" class="form-control form-control-modern" placeholder="Contoh: Hari TB Sedunia" required>
                        </div>
                        <div class="col-md-4">
                            <label class="small fw-bold text-muted mb-1">Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-select form-control-modern" required>
                                <option value="Belum">Belum</option>
                                <option value="Proses">Proses</option>
                                <option value="Selesai">Selesai</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="small fw-bold text-muted mb-1">Request By <span class="text-danger">*</span></label>
                            <select name="request_by" class="form-select form-control-modern" required>
                                <option value="">Pilih Pengusul...</option>
                                @foreach($requests as $req)
                                    <option value="{{ $req->name }}">{{ $req->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="small fw-bold text-muted mb-1">Output <span class="text-danger">*</span></label>
                            <select name="output" class="form-select form-control-modern" required>
                                <option value="">Pilih Output...</option>
                                @foreach($outputs as $out)
                                    <option value="{{ $out->name }}">{{ $out->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="small fw-bold text-muted mb-1">Rencana Kegiatan <span class="text-danger">*</span></label>
                            <textarea name="rencana_kegiatan" class="form-control form-control-modern" rows="2" placeholder="Jelaskan detail kegiatan..." required></textarea>
                        </div>
                        <div class="col-12">
                            <label class="small fw-bold text-muted mb-1">Bukti / Link (Opsional)</label>
                            <input type="text" name="bukti" class="form-control form-control-modern" placeholder="Link Google Drive, YouTube, dll">
                        </div>
                    </div>
                </div>
                <div class="modal-footer-modern p-3 d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-light shadow-sm btn-modern" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-modern-primary px-4">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection