@extends('layouts.admin')

@section('title', 'Data Keluhan')

@section('content')
{{-- CSS Select2 & Tema Bootstrap 5 --}}
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

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
        background-color: #10b981; /* Emerald green from reference */
        color: white;
        border: none;
        border-radius: 8px;
        padding: 0.5rem 1rem;
        font-weight: 600;
        transition: all 0.2s;
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
        padding: 1rem 1rem;
        border-bottom-color: #f3f4f6;
        vertical-align: middle;
        background-color: transparent !important;
    }
    .table-modern thead th {
        text-transform: uppercase;
        font-size: 0.75rem;
        font-weight: 600;
        color: #6b7280;
        border-bottom: 2px solid #f3f4f6 !important;
        letter-spacing: 0.5px;
    }
    .table-modern tbody tr:hover { background-color: #f8fafc !important; }

    /* Soft Badges for Status & Grades */
    .badge-soft { border-radius: 20px; padding: 0.35rem 0.8rem; font-weight: 600; font-size: 0.75rem; }
    .badge-soft-success { background-color: #d1fae5; color: #065f46; }
    .badge-soft-warning { background-color: #fef3c7; color: #92400e; }
    .badge-soft-danger { background-color: #fee2e2; color: #991b1b; }
    .badge-soft-primary { background-color: #dbeafe; color: #1e40af; }
    .badge-soft-secondary { background-color: #f3f4f6; color: #374151; }

    /* Form & Filter Restyling */
    .filter-wrapper { background-color: #ffffff; border-radius: 12px; }
    .form-control-modern, .form-select-modern {
        border-radius: 8px; border: 1px solid #e5e7eb; font-size: 0.85rem; padding: 0.5rem 0.75rem;
    }
    .form-control-modern:focus, .form-select-modern:focus {
        border-color: #10b981; box-shadow: 0 0 0 3px rgba(16,185,129,0.1);
    }
    .input-group-text-modern { background-color: #f9fafb; border: 1px solid #e5e7eb; color: #6b7280; }
    
    /* Select2 override */
    .select2-container--bootstrap-5 .select2-selection {
        border-radius: 8px !important; border: 1px solid #e5e7eb !important; min-height: 36px; padding-top: 2px;
    }
    .select2-container--bootstrap-5.select2-container--focus .select2-selection {
        border-color: #10b981 !important; box-shadow: 0 0 0 3px rgba(16,185,129,0.1) !important;
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
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
            <div>
                <h5 class="text-title m-0">Daftar Pengaduan</h5>
                <small class="text-muted">Kelola dan pantau semua data keluhan yang masuk</small>
            </div>
            
            @if(auth()->user()->role !== 'relationship')
            <button type="button" class="btn-modern-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#modalInput">    
                <i class="bi bi-plus-lg me-1"></i> Buat Laporan Baru
            </button>
            @endif
        </div>

        {{-- FILTER SECTION --}}
        <div class="filter-wrapper mb-4">
            <form action="{{ route('complaints.index') }}" method="GET">
                <div class="row g-2 align-items-center">
                    
                    {{-- Date Range --}}
                    <div class="col-12 col-xl-4 col-lg-5">
                        <div class="input-group input-group-sm shadow-sm" style="border-radius: 8px; overflow: hidden;">
                            <span class="input-group-text input-group-text-modern border-end-0"><i class="bi bi-calendar3"></i></span>
                            <input type="date" name="start_date" class="form-control form-control-modern border-start-0 border-end-0" value="{{ request('start_date') }}" title="Mulai" style="border-radius: 0;">
                            <span class="input-group-text input-group-text-modern border-start-0 border-end-0">s/d</span>
                            <input type="date" name="end_date" class="form-control form-control-modern border-start-0" value="{{ request('end_date') }}" title="Sampai" style="border-radius: 0;">
                        </div>
                    </div>

                    {{-- Select Unit --}}
                    <div class="col-6 col-md-3 col-xl-2">
                        <select name="unit_destination" class="form-select select2-filter" onchange="this.form.submit()">
                            <option value="">Semua Unit -</option>
                            @foreach($unitDestinations as $unit)
                                <option value="{{ $unit->name }}" {{ request('unit_destination') == $unit->name ? 'selected' : '' }}>{{ $unit->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Search --}}
                    <div class="col-6 col-md-4 col-xl-3">
                        <div class="input-group input-group-sm shadow-sm" style="border-radius: 8px; overflow: hidden;">
                            <span class="input-group-text input-group-text-modern border-end-0"><i class="bi bi-search"></i></span>
                            <input type="text" name="search" class="form-control form-control-modern border-start-0" placeholder="Cari keluhan..." value="{{ request('search') }}">
                        </div>
                    </div>

                    {{-- Sort --}}
                    <div class="col-6 col-md-2 col-xl-auto">
                        <select name="sort" class="form-select form-control-modern shadow-sm" onchange="this.form.submit()">
                            <option value="terbaru" {{ request('sort') == 'terbaru' ? 'selected' : '' }}>Terbaru</option>
                            <option value="terlama" {{ request('sort') == 'terlama' ? 'selected' : '' }}>Terlama</option>
                        </select>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="col-12 col-xl-auto d-flex gap-2 ms-auto mt-3 mt-xl-0">
                        <button type="submit" class="btn btn-action-icon bg-light text-dark shadow-sm border" title="Terapkan Filter"><i class="bi bi-funnel-fill"></i></button>
                        <a href="{{ route('complaints.index') }}" class="btn btn-action-icon bg-light text-danger shadow-sm border" title="Reset Filter"><i class="bi bi-x-lg"></i></a>
                        
                        <div class="vr mx-1"></div>

                        <button type="button" class="btn btn-modern-primary d-flex align-items-center gap-2" style="padding: 0.35rem 0.75rem;" data-bs-toggle="modal" data-bs-target="#modalImport" title="Import Excel">
                            <i class="bi bi-cloud-upload"></i> <span class="d-none d-sm-inline font-weight-normal" style="font-size: 0.85rem;">Import</span>
                        </button>
                        <a href="{{ route('export.excel', request()->query()) }}" class="btn btn-action-icon bg-success-subtle text-success shadow-sm" title="Download Excel"><i class="bi bi-file-earmark-excel-fill"></i></a>
                        <a href="{{ route('export.pdf', request()->query()) }}" target="_blank" class="btn btn-action-icon bg-danger-subtle text-danger shadow-sm" title="Download PDF"><i class="bi bi-file-earmark-pdf-fill"></i></a>
                    </div>
                </div>
            </form>
        </div>

        {{-- ALERTS --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" style="border-radius: 12px; background-color: #d1fae5; color: #065f46;">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" style="border-radius: 12px; background-color: #fee2e2; color: #991b1b;">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- MAIN TABLE --}}
        <div class="table-responsive border rounded-3" style="border-color: #f3f4f6 !important;">
            <table class="table table-modern align-middle table-hover">
                <thead class="bg-light">
                    <tr>
                        <th class="text-center" width="5%">No</th>
                        <th width="10%">Tanggal</th>
                        <th width="18%">Pelapor</th>
                        <th class="text-center" width="8%">Media</th>
                        <th class="text-center" width="8%">Grade</th>
                        <th width="22%">Detail Keluhan</th>
                        <th width="12%">Unit Tujuan</th>
                        <th class="text-center" width="10%">Status</th>
                        <th class="text-center" width="7%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($complaints as $complaint)
                    <tr>
                        <td class="text-center text-muted fw-bold">{{ $loop->iteration + ($complaints->currentPage() - 1) * $complaints->perPage() }}</td>
                        <td>
                            <div class="fw-bold text-dark">{{ \Carbon\Carbon::parse($complaint->date)->format('d M Y') }}</div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="bg-light rounded-circle d-flex align-items-center justify-content-center text-primary fw-bold" style="width: 35px; height: 35px; font-size: 0.8rem;">
                                    {{ substr($complaint->reporter_type, 0, 2) }}
                                </div>
                                <div>
                                    <div class="fw-bold text-dark" style="font-size: 0.85rem;">{{ $complaint->reporter_type }}</div>
                                    @if($complaint->reporter_name)
                                        <div class="text-muted" style="font-size: 0.75rem;">{{ $complaint->reporter_name }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="text-center">
                            <span class="badge-soft badge-soft-secondary border shadow-sm">{{ $complaint->source->name ?? '-' }}</span>
                        </td>
                        
                        {{-- GRADE (Modernized) --}}
                        <td class="text-center">
                            @php
                                $isHex = Str::startsWith($complaint->grade, '#'); 
                                $colorClass = 'badge-soft-secondary';
                                if(!$isHex) {
                                    if(str_contains(strtolower($complaint->grade), 'merah')) $colorClass = 'badge-soft-danger';
                                    elseif(str_contains(strtolower($complaint->grade), 'kuning')) $colorClass = 'badge-soft-warning';
                                    elseif(str_contains(strtolower($complaint->grade), 'hijau')) $colorClass = 'badge-soft-success';
                                }
                            @endphp
                            
                            @if($isHex)
                                <div class="d-inline-block rounded-circle shadow-sm" style="background-color: {{ $complaint->grade }}; width: 14px; height: 14px;" title="{{ $complaint->grade }}" data-bs-toggle="tooltip"></div>
                            @else
                                <span class="badge-soft {{ $colorClass }}" title="{{ $complaint->grade }}"><i class="bi bi-circle-fill" style="font-size: 8px; vertical-align: middle;"></i></span>
                            @endif
                        </td>

                        {{-- DETAIL KELUHAN DINAMIS --}}
                        <td>
                            <div class="d-flex flex-wrap gap-1 mb-2">
                                @if(!empty($complaint->detail_keluhan) && is_array($complaint->detail_keluhan))
                                    @foreach($complaint->detail_keluhan as $catName => $items)
                                        <span class="badge-soft badge-soft-secondary border" style="font-size: 0.65rem;">{{ $catName }}</span>
                                    @endforeach
                                @else
                                    <span class="text-muted fst-italic" style="font-size: 0.75rem;">Lainnya / Ekstra</span>
                                @endif
                            </div>
                            <button class="btn btn-sm btn-light border text-primary rounded-pill fw-bold" style="font-size: 0.7rem; padding: 2px 10px;" data-bs-toggle="modal" data-bs-target="#modalDetail{{ $complaint->id }}">
                                Lihat Lengkap <i class="bi bi-chevron-right"></i>
                            </button>
                        </td>

                        <td><span class="fw-bold text-dark" style="font-size: 0.85rem;">{{ $complaint->unit_destination }}</span></td>
                        
                        {{-- STATUS --}}
                        <td class="text-center">
                            @if($complaint->status == 'Pending')
                                <span class="badge-soft badge-soft-warning"><i class="bi bi-hourglass-split me-1"></i>Pending</span>
                            @elseif($complaint->status == 'Proses')
                                <span class="badge-soft badge-soft-primary"><i class="bi bi-arrow-repeat me-1"></i>Proses</span>
                            @else
                                <span class="badge-soft badge-soft-success"><i class="bi bi-check2-circle me-1"></i>Selesai</span>
                            @endif
                        </td>

                        {{-- AKSI --}}
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-1">
                                <button type="button" class="btn btn-action-icon bg-warning-subtle text-warning shadow-sm" data-bs-toggle="modal" data-bs-target="#modalEdit{{ $complaint->id }}" title="Edit">
                                    <i class="bi bi-pencil-fill"></i>
                                </button>
                                @if(auth()->user()->role == 'admin')
                                    <form action="{{ route('complaints.destroy', $complaint->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus data ini?');" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-action-icon bg-danger-subtle text-danger shadow-sm" title="Hapus"><i class="bi bi-trash-fill"></i></button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>

                    {{-- MODAL LIHAT DETAIL KELUHAN --}}
                    <div class="modal fade" id="modalDetail{{ $complaint->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                            <div class="modal-content shadow-lg">
                                <div class="modal-header-modern d-flex justify-content-between align-items-center">
                                    <h6 class="modal-title-modern m-0"><i class="bi bi-layout-text-sidebar-reverse text-primary me-2"></i> Rincian Lengkap Pengaduan</h6>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body p-4 bg-light" style="max-height: 70vh; overflow-y: auto;">
                                    
                                    <h6 class="fw-bold text-dark mb-3" style="font-size: 0.9rem;">Item yang Dikeluhkan:</h6>
                                    <div class="row g-3">
                                        @if(!empty($complaint->detail_keluhan) && is_array($complaint->detail_keluhan))
                                            @foreach($complaint->detail_keluhan as $catName => $items)
                                            <div class="col-md-6">
                                                <div class="card border-0 shadow-sm h-100" style="border-radius: 10px !important;">
                                                    <div class="card-header bg-white fw-bold text-primary py-2 border-bottom-0" style="font-size: 0.8rem;">{{ $catName }}</div>
                                                    <div class="card-body p-3 pt-0 text-muted" style="font-size: 0.8rem;">
                                                        <ul class="mb-0 ps-3">
                                                            @foreach($items as $item) <li>{{ $item }}</li> @endforeach
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                        @else
                                            <div class="col-12"><p class="text-muted fst-italic text-center bg-white p-3 rounded shadow-sm">Tidak ada checklist kategori yang dipilih.</p></div>
                                        @endif
                                    </div>

                                    <div class="mt-4">
                                        <h6 class="fw-bold text-dark mb-2" style="font-size: 0.9rem;">Deskripsi Tambahan (Lainnya):</h6>
                                        <div class="p-3 bg-white shadow-sm rounded-3 text-muted" style="font-size: 0.85rem; white-space: pre-line; border: 1px solid #f3f4f6;">
                                            {{ $complaint->description ?: 'Tidak ada deskripsi tambahan.' }}
                                        </div>
                                    </div>

                                    <div class="mt-4">
                                        <h6 class="fw-bold text-success mb-2" style="font-size: 0.9rem;">Tindak Lanjut / Solusi:</h6>
                                        <div class="p-3 bg-success-subtle shadow-sm rounded-3 text-dark" style="font-size: 0.85rem; white-space: pre-line;">
                                            {{ $complaint->answer ?: 'Belum ada tindak lanjut / jawaban dari unit.' }}
                                        </div>
                                    </div>

                                </div>
                                <div class="modal-footer-modern p-3 text-end">
                                    <button type="button" class="btn btn-light shadow-sm btn-modern" data-bs-dismiss="modal">Tutup Rincian</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- END MODAL DETAIL --}}

                    {{-- MODAL EDIT --}}
                    <div class="modal fade" id="modalEdit{{ $complaint->id }}" tabindex="-1" data-bs-backdrop="static">
                        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                            <div class="modal-content shadow-lg">
                                <div class="modal-header-modern d-flex justify-content-between align-items-center border-bottom-warning">
                                    <h6 class="modal-title-modern m-0"><i class="bi bi-pencil-square text-warning me-2"></i> Edit Data Pengaduan</h6>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <form action="{{ route('complaints.update', $complaint->id) }}" method="POST">
                                    @csrf @method('PUT')
                                    <div class="modal-body p-4 bg-light" style="max-height: 70vh; overflow-y: auto;">
                                        <div class="card border-0 shadow-sm p-4 mb-4">
                                            <div class="row g-3">
                                                <div class="col-md-3">
                                                    <label class="small fw-bold text-muted mb-1">Tanggal</label>
                                                    <input type="date" name="date" class="form-control form-control-modern" value="{{ \Carbon\Carbon::parse($complaint->date)->format('Y-m-d') }}" required>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="small fw-bold text-muted mb-1">Unit Pelapor</label>
                                                    <select name="reporter_type" class="form-select select2-modal" required>
                                                        @foreach($reporterTypes as $type)
                                                            <option value="{{ $type->name }}" {{ $complaint->reporter_type == $type->name ? 'selected' : '' }}>{{ $type->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="small fw-bold text-muted mb-1">Media</label>
                                                    <select name="source_id" class="form-select select2-modal" required>
                                                        @foreach($sources as $source)
                                                            <option value="{{ $source->id }}" {{ $complaint->source_id == $source->id ? 'selected' : '' }}>{{ $source->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="small fw-bold text-muted mb-1">Nama Pelapor</label>
                                                    <input type="text" name="reporter_name" class="form-control form-control-modern" value="{{ $complaint->reporter_name }}">
                                                </div>
                                            </div>
                                        </div>

                                        <h6 class="fw-bold text-primary mb-3"><i class="bi bi-list-check me-2"></i>Detail Keluhan (Dinamic Checklist)</h6>
                                        <div class="row g-3 mb-4">
                                            @php $savedDetails = is_array($complaint->detail_keluhan) ? $complaint->detail_keluhan : []; @endphp
                                            @foreach($kategoriKeluhan as $kategori)
                                            <div class="col-md-4">
                                                <div class="card h-100 border-0 shadow-sm" style="border-radius: 12px !important;">
                                                    <div class="card-header bg-white fw-bold text-dark py-3 border-bottom-0">{{ $kategori->name }}</div>
                                                    <div class="card-body pt-0 text-muted" style="font-size: 0.85rem;">
                                                        @foreach($kategori->items as $item)
                                                        <div class="form-check custom-checkbox mb-2">
                                                            <input class="form-check-input" type="checkbox" 
                                                                name="detail_keluhan[{{ $kategori->name }}][]" 
                                                                value="{{ $item->name }}" 
                                                                id="edit_item_{{ $complaint->id }}_{{ $item->id }}"
                                                                {{ isset($savedDetails[$kategori->name]) && in_array($item->name, $savedDetails[$kategori->name]) ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="edit_item_{{ $complaint->id }}_{{ $item->id }}">{{ $item->name }}</label>
                                                        </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>

                                        <div class="card border-0 shadow-sm p-4">
                                            <div class="row g-3 mb-3">
                                                <div class="col-md-6">
                                                    <label class="small fw-bold text-muted mb-1">Deskripsi Tambahan / Lain-lain</label>
                                                    <textarea name="description" class="form-control form-control-modern" rows="3">{{ $complaint->description }}</textarea>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="small fw-bold text-muted mb-1">Tindak Lanjut / Solusi</label>
                                                    <textarea name="answer" class="form-control form-control-modern" rows="3">{{ $complaint->answer }}</textarea>
                                                </div>
                                            </div>
                                            <div class="row g-3">
                                                <div class="col-md-4">
                                                    <label class="small fw-bold text-muted mb-1">Unit Tujuan</label>
                                                    <select name="unit_destination" class="form-select select2-modal" required>
                                                        @foreach($unitDestinations as $unit)
                                                            <option value="{{ $unit->name }}" {{ $complaint->unit_destination == $unit->name ? 'selected' : '' }}>{{ $unit->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="small fw-bold text-muted mb-1">Tingkat Kegawatan</label>
                                                    <select name="grade" class="form-select select2-modal">
                                                        @foreach($grades as $grade)
                                                            <option value="{{ $grade->color_class ?? $grade->name }}" {{ $complaint->grade == ($grade->color_class ?? $grade->name) ? 'selected' : '' }}>
                                                                {{ $grade->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="small fw-bold text-muted mb-1">Status</label>
                                                    <select name="status" class="form-select select2-modal">
                                                        <option value="Pending" {{ $complaint->status == 'Pending' ? 'selected' : '' }}>Pending</option>
                                                        <option value="Proses" {{ $complaint->status == 'Proses' ? 'selected' : '' }}>Proses</option>
                                                        <option value="Selesai" {{ $complaint->status == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                                                    </select>
                                                </div>
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
                    {{-- END MODAL EDIT --}}

                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-5">
                            <div class="text-muted d-flex flex-column align-items-center">
                                <i class="bi bi-folder2-open fs-1 text-light mb-2"></i>
                                <span>Belum ada data pengaduan yang ditemukan.</span>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4 d-flex justify-content-end">
            {{ $complaints->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>

{{-- MODAL IMPORT EXCEL --}}
<div class="modal fade" id="modalImport" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg">
            <div class="modal-header-modern d-flex justify-content-between align-items-center">
                <h6 class="modal-title-modern m-0"><i class="bi bi-file-earmark-excel text-success me-2"></i> Import Data Excel</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('complaints.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4 bg-light">
                    <div class="card border-0 shadow-sm p-4 text-center mb-4">
                        <i class="bi bi-cloud-arrow-down text-success mb-2" style="font-size: 2rem;"></i>
                        <p class="fw-bold text-dark mb-2">Belum punya format template Excel?</p>
                        <a href="{{ asset('template_pengaduan.xlsx') }}" class="btn btn-outline-success btn-modern w-100 shadow-sm" download>
                            Download Template Disini
                        </a>
                        <small class="text-muted mt-3">Silakan isi data sesuai format template lalu upload kembali di bawah ini.</small>
                    </div>
                    <div class="mb-2">
                        <label class="form-label fw-bold text-dark small">Pilih File Excel (.xlsx / .xls)</label>
                        <input type="file" name="file" class="form-control form-control-modern bg-white" required>
                    </div>
                </div>
                <div class="modal-footer-modern p-3 d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-light btn-modern shadow-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success btn-modern shadow-sm px-4"><i class="bi bi-upload me-1"></i> Mulai Import</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL INPUT BARU --}}
<div class="modal fade" id="modalInput" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content shadow-lg">
            <div class="modal-header-modern d-flex justify-content-between align-items-center">
                <h6 class="modal-title-modern m-0"><i class="bi bi-plus-circle-fill text-primary me-2"></i> Buat Laporan Pengaduan Baru</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            
            <form action="{{ route('complaints.store') }}" method="POST" id="formSimpan" onsubmit="showLoading()">
                @csrf
                <div class="modal-body p-4 bg-light" style="max-height: 70vh; overflow-y: auto;">
                    
                    {{-- INFO IDENTITAS --}}
                    <div class="card border-0 shadow-sm p-4 mb-4">
                        <h6 class="fw-bold text-dark mb-3 border-bottom pb-2" style="font-size: 0.9rem;">Identitas Pelapor</h6>
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="small fw-bold text-muted mb-1">Tanggal <span class="text-danger">*</span></label>
                                <input type="date" name="date" class="form-control form-control-modern" value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-3">
                                <label class="small fw-bold text-muted mb-1">Unit Pelapor <span class="text-danger">*</span></label>
                                <select name="reporter_type" class="form-select select2-modal" required>
                                    <option value=""></option>
                                    @foreach($reporterTypes as $type) <option value="{{ $type->name }}">{{ $type->name }}</option> @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="small fw-bold text-muted mb-1">Media <span class="text-danger">*</span></label>
                                <select name="source_id" class="form-select select2-modal" required>
                                    <option value=""></option>
                                    @foreach($sources as $source) <option value="{{ $source->id }}">{{ $source->name }}</option> @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="small fw-bold text-muted mb-1">Nama Pelapor (Opsional)</label>
                                <input type="text" name="reporter_name" class="form-control form-control-modern" placeholder="Masukkan nama...">
                            </div>
                        </div>
                    </div>

                    {{-- AREA CHECKLIST KELUHAN --}}
                    <h6 class="fw-bold text-primary mb-3"><i class="bi bi-list-check me-2"></i>Detail Keluhan (Centang yang sesuai)</h6>
                    <div class="row g-3 mb-4">
                        @foreach($kategoriKeluhan as $kategori)
                        <div class="col-md-4">
                            <div class="card h-100 shadow-sm border-0" style="border-radius: 12px !important;">
                                <div class="card-header bg-white fw-bold text-dark py-3 border-bottom-0">{{ $kategori->name }}</div>
                                <div class="card-body pt-0 text-muted" style="font-size: 0.85rem;">
                                    @foreach($kategori->items as $item)
                                    <div class="form-check custom-checkbox mb-2">
                                        <input class="form-check-input" type="checkbox" name="detail_keluhan[{{ $kategori->name }}][]" value="{{ $item->name }}" id="item_{{ $item->id }}">
                                        <label class="form-check-label" for="item_{{ $item->id }}">{{ $item->name }}</label>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    {{-- LAIN-LAIN & TUJUAN --}}
                    <div class="card border-0 shadow-sm p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="small fw-bold text-muted mb-1">Lain-lain / Deskripsi Tambahan</label>
                                <textarea name="description" class="form-control form-control-modern" rows="4" placeholder="Tuliskan jika ada keluhan di luar pilihan di atas..."></textarea>
                            </div>
                            <div class="col-md-6">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="small fw-bold text-muted mb-1">Unit Tujuan <span class="text-danger">*</span></label>
                                        <select name="unit_destination" class="form-select select2-modal" required>
                                            <option value=""></option>
                                            @foreach($unitDestinations as $unit) <option value="{{ $unit->name }}">{{ $unit->name }}</option> @endforeach
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <label class="small fw-bold text-muted mb-1">Grade (Kegawatan) <span class="text-danger">*</span></label>
                                        <select name="grade" class="form-select select2-modal" required>
                                            <option value=""></option>
                                            @foreach($grades as $grade) <option value="{{ $grade->color_class ?? $grade->name }}">{{ $grade->name }}</option> @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer-modern p-3 d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-light shadow-sm btn-modern" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-modern-primary px-4" id="btnSubmit">
                        <i class="bi bi-save me-1"></i> Simpan Laporan
                    </button>
                    <button type="button" class="btn btn-modern-primary px-4 d-none" id="btnLoading" disabled>
                        <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Menyimpan...
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- LIBRARY JQUERY & SELECT2 JS --}}
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    function showLoading() {
        document.getElementById('btnSubmit').classList.add('d-none');
        document.getElementById('btnLoading').classList.remove('d-none');
    }

    $(document).ready(function() {
        // Initialize tooltip if you're using Bootstrap bundle JS
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })

        // 1. Inisialisasi Select2 untuk Form Filter (di luar modal)
        $('.select2-filter').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: 'Pilih...',
            allowClear: true
        });

        // 2. Inisialisasi Select2 di dalam Modal Input Baru
        $('#modalInput').on('shown.bs.modal', function () {
            $(this).find('.select2-modal').select2({
                theme: 'bootstrap-5',
                dropdownParent: $('#modalInput'),
                width: '100%',
                placeholder: 'Silahkan Pilih'
            });
        });

        // 3. Inisialisasi Select2 di dalam Modal Edit (Karena modal dinamis)
        $('[id^=modalEdit]').on('shown.bs.modal', function () {
            let modalId = $(this).attr('id');
            $(this).find('.select2-modal').select2({
                theme: 'bootstrap-5',
                dropdownParent: $('#' + modalId),
                width: '100%'
            });
        });
    });
</script>

@endsection