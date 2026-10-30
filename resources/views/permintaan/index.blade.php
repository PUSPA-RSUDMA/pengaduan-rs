@extends('layouts.admin')

@section('title', 'Data Layanan Informasi')

@section('content')

{{-- Tambahkan CSS Select2 & Tema Bootstrap 5 --}}
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
        background-color: #10b981; /* Emerald green */
        color: white; border: none; border-radius: 8px;
        padding: 0.5rem 1rem; font-weight: 600; transition: all 0.2s;
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
    .badge-soft-success { background-color: #d1fae5; color: #065f46; }
    .badge-soft-info { background-color: #e0f2fe; color: #0369a1; }
    .badge-soft-warning { background-color: #fef3c7; color: #92400e; }
    .badge-soft-danger { background-color: #fee2e2; color: #991b1b; }
    .badge-soft-primary { background-color: #dbeafe; color: #1e40af; }
    .badge-soft-secondary { background-color: #f3f4f6; color: #374151; }

    /* Form & Filter Restyling */
    .filter-wrapper { background-color: #ffffff; border-radius: 12px; border: 1px solid #f3f4f6; }
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

    /* Accordion Modernization */
    .accordion-item { border: 1px solid #e5e7eb !important; border-radius: 12px !important; overflow: hidden; margin-bottom: 0.75rem; }
    .accordion-button { background-color: #f9fafb; font-weight: 600; color: #374151; box-shadow: none !important; }
    .accordion-button:not(.collapsed) { background-color: #ecfdf5; color: #065f46; border-bottom: 1px solid #e5e7eb; }
</style>

<div class="card border-0 mb-4">
    <div class="card-body p-4">
        {{-- HEADER SECTION --}}
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
            <div>
                <h5 class="text-title m-0">Daftar Layanan Informasi</h5>
                <small class="text-muted">Kelola data permintaan layanan dan informasi pelanggan</small>
            </div>
            
            <button type="button" class="btn-modern-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#modalInput">   
                <i class="bi bi-plus-lg me-1"></i> Buat Baru
            </button>
        </div>

        {{-- FILTER SECTION --}}
        <div class="filter-wrapper p-3 mb-4 shadow-sm">
            <form action="{{ route('permintaan.index') }}" method="GET">
                <div class="row g-2 align-items-center">
                    
                    {{-- 1. FILTER TANGGAL --}}
                    <div class="col-12 col-xl-4 col-lg-5">
                        <div class="input-group input-group-sm shadow-sm" style="border-radius: 8px; overflow: hidden;">
                            <span class="input-group-text input-group-text-modern border-end-0"><i class="bi bi-calendar3"></i></span>
                            <input type="date" name="start_date" class="form-control form-control-modern border-start-0 border-end-0" value="{{ request('start_date') }}" title="Mulai" style="border-radius: 0;">
                            <span class="input-group-text input-group-text-modern border-start-0 border-end-0">s/d</span>
                            <input type="date" name="end_date" class="form-control form-control-modern border-start-0" value="{{ request('end_date') }}" title="Sampai" style="border-radius: 0;">
                        </div>
                    </div>

                    {{-- 2. UNIT TERKAIT --}}
                    <div class="col-6 col-md-3 col-xl-2">
                        <select name="unit_terkait" class="form-select form-control-modern" onchange="this.form.submit()">
                            <option value="">Semua Unit -</option>
                            @foreach($unitDestinations as $unit)
                                <option value="{{ $unit->name }}" {{ request('unit_terkait') == $unit->name ? 'selected' : '' }}>
                                    {{ $unit->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- 3. SEARCH --}}
                    <div class="col-6 col-md-4 col-xl-3">
                        <div class="input-group input-group-sm shadow-sm" style="border-radius: 8px; overflow: hidden;">
                            <span class="input-group-text input-group-text-modern border-end-0"><i class="bi bi-search"></i></span>
                            <input type="text" name="search" class="form-control form-control-modern border-start-0" placeholder="Cari No HP / Uraian..." value="{{ request('search') }}">
                        </div>
                    </div>

                    {{-- 4. SORT --}}
                    <div class="col-6 col-md-2 col-xl-auto">
                        <select name="sort" class="form-select form-control-modern shadow-sm" onchange="this.form.submit()">
                            <option value="terbaru" {{ request('sort') == 'terbaru' ? 'selected' : '' }}>Terbaru</option>
                            <option value="terlama" {{ request('sort') == 'terlama' ? 'selected' : '' }}>Terlama</option>
                        </select>
                    </div>

                    {{-- 5. TOMBOL FILTER --}}
                    <div class="col-12 col-xl-auto d-flex gap-2 ms-auto mt-3 mt-xl-0">
                        <button type="submit" class="btn btn-action-icon bg-light text-dark shadow-sm border" title="Terapkan Filter"><i class="bi bi-funnel-fill"></i></button>
                        <a href="{{ route('permintaan.index') }}" class="btn btn-action-icon bg-light text-danger shadow-sm border" title="Reset Filter"><i class="bi bi-x-lg"></i></a>
                        
                        <div class="vr mx-1"></div>

                        <a href="{{ route('permintaan.export.excel', request()->query()) }}" class="btn btn-action-icon bg-success-subtle text-success shadow-sm" title="Download Excel"><i class="bi bi-file-earmark-excel-fill"></i></a>
                        <a href="{{ route('permintaan.export.pdf', request()->query()) }}" target="_blank" class="btn btn-action-icon bg-danger-subtle text-danger shadow-sm" title="Download PDF"><i class="bi bi-file-earmark-pdf-fill"></i></a>
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

        {{-- MAIN TABLE --}}
        <div class="table-responsive border rounded-3" style="border-color: #f3f4f6 !important;">
            <table class="table table-modern align-middle table-hover">
                <thead class="bg-light text-center">
                    <tr>
                        <th width="5%">No</th>
                        <th>Tgl Masuk</th>
                        <th>No HP</th>
                        <th>Metode</th>
                        <th>Jenis Permintaan</th>
                        <th width="20%">Detail Keluhan</th>
                        <th width="22%" class="text-start">Uraian</th>
                        <th>Unit Terkait</th>
                        <th>Tgl Verifikasi</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-center">
                    @forelse($permintaans as $item)
                    <tr>
                        <td class="text-muted fw-bold">{{ $loop->iteration + ($permintaans->currentPage() - 1) * $permintaans->perPage() }}</td>
                        <td>
                            <div class="fw-bold text-dark">{{ \Carbon\Carbon::parse($item->tgl_masuk)->format('d M Y') }}</div>
                        </td>
                        <td>
                            <span class="fw-bold text-dark">{{ $item->no_hp }}</span>
                        </td>
                        <td>
                            @if($item->metode_penyampaian == 'Chat')
                                <span class="badge-soft badge-soft-success border border-success-subtle"><i class="bi bi-whatsapp me-1"></i> Chat</span>
                            @else
                                <span class="badge-soft badge-soft-info border border-info-subtle"><i class="bi bi-telephone-fill me-1"></i> Telfon</span>
                            @endif
                        </td>
                        <td>
                            @if($item->jenis_permintaan == 'Pengaduan')
                                <span class="badge-soft badge-soft-danger">Pengaduan</span>
                            @else
                                <span class="badge-soft badge-soft-primary">Informasi</span>
                            @endif
                        </td>

                        {{-- KOLOM DETAIL KELUHAN DINAMIS --}}
                        <td>
                            <div class="mb-2 d-flex flex-wrap justify-content-center gap-1">
                                @if(!empty($item->detail_keluhan) && is_array($item->detail_keluhan))
                                    @foreach($item->detail_keluhan as $catName => $subItems)
                                        <span class="badge-soft badge-soft-secondary border shadow-sm" style="font-size: 0.65rem;">{{ $catName }}</span>
                                    @endforeach
                                @else
                                    <span class="text-muted fst-italic" style="font-size: 0.7rem;">-</span>
                                @endif
                            </div>
                            <button class="btn btn-sm btn-light border text-primary rounded-pill fw-bold py-1 px-3 shadow-sm" style="font-size: 0.7rem;" data-bs-toggle="modal" data-bs-target="#modalDetail{{ $item->id }}">
                                Lihat Lengkap <i class="bi bi-chevron-right ms-1"></i>
                            </button>
                        </td>

                        <td class="text-start">
                            <span class="text-muted" style="font-size: 0.85rem;">{{ Str::limit($item->uraian, 70) }}</span>
                        </td>
                        <td><span class="fw-bold text-dark" style="font-size: 0.85rem;">{{ $item->unit_terkait }}</span></td>
                        
                        <td>
                            @if($item->tgl_verifikasi)
                                <div class="badge-soft badge-soft-success"><i class="bi bi-check-circle-fill me-1"></i> {{ \Carbon\Carbon::parse($item->tgl_verifikasi)->format('d/m/Y') }}</div>
                            @else
                                <span class="badge-soft badge-soft-secondary"><i class="bi bi-hourglass-split me-1"></i> Belum</span>
                            @endif
                        </td>
                        
                        <td>
                            <div class="d-flex justify-content-center gap-1">
                                <button type="button" class="btn btn-action-icon bg-warning-subtle text-warning shadow-sm" data-bs-toggle="modal" data-bs-target="#modalEdit{{ $item->id }}" title="Edit">
                                    <i class="bi bi-pencil-fill"></i>
                                </button>
                                <form action="{{ route('permintaan.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus data ini?');" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-action-icon bg-danger-subtle text-danger shadow-sm" title="Hapus"><i class="bi bi-trash-fill"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>

                    {{-- MODAL LIHAT DETAIL KELUHAN --}}
                    <div class="modal fade" id="modalDetail{{ $item->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-centered">
                            <div class="modal-content shadow-lg">
                                <div class="modal-header-modern d-flex justify-content-between align-items-center">
                                    <h6 class="modal-title-modern m-0"><i class="bi bi-card-text text-info me-2"></i> Rincian Detail Permintaan</h6>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body p-4 bg-light">
                                    <div class="row g-3">
                                        @if(!empty($item->detail_keluhan) && is_array($item->detail_keluhan))
                                            @foreach($item->detail_keluhan as $catName => $subItems)
                                            <div class="col-md-6">
                                                <div class="card border-0 shadow-sm h-100" style="border-radius: 12px !important;">
                                                    <div class="card-header bg-white fw-bold text-primary py-2 border-bottom-0" style="font-size: 0.85rem;">{{ $catName }}</div>
                                                    <div class="card-body p-3 pt-0 text-muted" style="font-size: 0.85rem;">
                                                        <ul class="mb-0 ps-3">
                                                            @foreach($subItems as $subItem) <li>{{ $subItem }}</li> @endforeach
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                        @else
                                            <div class="col-12"><p class="text-muted fst-italic text-center bg-white p-3 rounded shadow-sm">Tidak ada kategori detail yang dipilih.</p></div>
                                        @endif
                                    </div>
                                </div>
                                <div class="modal-footer-modern p-3 text-end">
                                    <button type="button" class="btn btn-light shadow-sm btn-modern" data-bs-dismiss="modal">Tutup</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- END MODAL DETAIL --}}

                    {{-- MODAL EDIT --}}
                    <div class="modal fade" id="modalEdit{{ $item->id }}" tabindex="-1" data-bs-backdrop="static">
                        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                            <div class="modal-content shadow-lg">
                                <div class="modal-header-modern border-bottom-warning d-flex justify-content-between align-items-center">
                                    <h6 class="modal-title-modern m-0"><i class="bi bi-pencil-square text-warning me-2"></i> Edit Data Layanan & Informasi</h6>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <form action="{{ route('permintaan.update', $item->id) }}" method="POST">
                                    @csrf @method('PUT')
                                    <div class="modal-body p-4 bg-light">
                                        <div class="card border-0 shadow-sm p-4 mb-4">
                                            <div class="row g-3">
                                                <div class="col-md-3">
                                                    <label class="small fw-bold text-muted mb-1">Tanggal Masuk</label>
                                                    <input type="date" name="tgl_masuk" class="form-control form-control-modern" value="{{ \Carbon\Carbon::parse($item->tgl_masuk)->format('Y-m-d') }}" required>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="small fw-bold text-muted mb-1">No HP</label>
                                                    <input type="number" name="no_hp" class="form-control form-control-modern" value="{{ $item->no_hp }}" required>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="small fw-bold text-muted mb-1">Metode Penyampaian</label>
                                                    <select name="metode_penyampaian" class="form-select form-select-modern" required>
                                                        <option value="Chat" {{ $item->metode_penyampaian == 'Chat' ? 'selected' : '' }}>Chat</option>
                                                        <option value="Telfon" {{ $item->metode_penyampaian == 'Telfon' ? 'selected' : '' }}>Telfon</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="small fw-bold text-muted mb-1">Jenis Permintaan</label>
                                                    <select name="jenis_permintaan" class="form-select form-select-modern" required>
                                                        <option value="Pengaduan" {{ $item->jenis_permintaan == 'Pengaduan' ? 'selected' : '' }}>Pengaduan</option>
                                                        <option value="Informasi" {{ $item->jenis_permintaan == 'Informasi' ? 'selected' : '' }}>Informasi</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- CHECKLIST DETAIL KELUHAN DINAMIS --}}
                                        <h6 class="fw-bold text-primary mb-3"><i class="bi bi-list-check me-2"></i>Detail Permintaan (Hanya Pilih dalam 1 Kategori)</h6>
                                        <div class="row g-3 mb-4 checklist-container">
                                            @php $savedDetails = is_array($item->detail_keluhan) ? $item->detail_keluhan : []; @endphp
                                            @foreach($kategoriPermintaan as $kategori)
                                            <div class="col-md-4">
                                                <div class="card h-100 border-0 shadow-sm" style="border-radius: 12px !important;">
                                                    <div class="card-header bg-white fw-bold text-dark py-3 border-bottom-0">{{ $kategori->name }}</div>
                                                    <div class="card-body pt-0 text-muted" style="font-size: 0.85rem;">
                                                        @foreach($kategori->items as $subItem)
                                                        <div class="form-check custom-checkbox mb-2">
                                                            <input class="form-check-input category-check" type="checkbox" 
                                                                data-category="{{ $kategori->name }}"
                                                                name="detail_keluhan[{{ $kategori->name }}][]" 
                                                                value="{{ $subItem->name }}" 
                                                                id="edit_permintaan_{{ $item->id }}_{{ $subItem->id }}"
                                                                {{ isset($savedDetails[$kategori->name]) && in_array($subItem->name, $savedDetails[$kategori->name]) ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="edit_permintaan_{{ $item->id }}_{{ $subItem->id }}">{{ $subItem->name }}</label>
                                                        </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>

                                        <div class="card border-0 shadow-sm p-4">
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label class="small fw-bold text-muted mb-1">Uraian</label>
                                                    <textarea name="uraian" class="form-control form-control-modern" rows="3" required>{{ $item->uraian }}</textarea>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="small fw-bold text-muted mb-1">Unit Terkait</label>
                                                    <select name="unit_terkait" class="form-select form-control-modern" required>
                                                        @foreach($unitDestinations as $unit)
                                                            <option value="{{ $unit->name }}" {{ $item->unit_terkait == $unit->name ? 'selected' : '' }}>{{ $unit->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="small fw-bold text-muted mb-1">Tanggal Verifikasi</label>
                                                    <input type="date" name="tgl_verifikasi" class="form-control form-control-modern text-success fw-bold" value="{{ $item->tgl_verifikasi ? \Carbon\Carbon::parse($item->tgl_verifikasi)->format('Y-m-d') : '' }}">
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
                        <td colspan="10" class="text-center py-5">
                            <div class="text-muted d-flex flex-column align-items-center">
                                <i class="bi bi-folder2-open fs-1 text-light mb-2"></i>
                                <span>Belum ada data yang ditemukan.</span>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4 d-flex justify-content-end">
            {{ $permintaans->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>

{{-- MODAL INPUT BARU (SISTEM AKORDION MULTI-ROW) --}}
<div class="modal fade" id="modalInput" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content shadow-lg">
            
            <div class="modal-header-modern d-flex justify-content-between align-items-center">
                <h6 class="modal-title-modern m-0">
                    <i class="bi bi-journal-plus text-primary me-2"></i> Input Data Baru (Multi-Row)
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            
            <form action="{{ route('permintaan.store') }}" method="POST" id="formSimpan" onsubmit="showLoading()">
                @csrf
                
                <div class="modal-body p-4 bg-light">
                    
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <small class="text-muted"><i class="bi bi-info-circle me-1"></i> Baris baru otomatis terbuka dan baris lainnya tertutup agar form tetap rapi.</small>
                        <button type="button" class="btn btn-sm text-success bg-success-subtle fw-bold border-success-subtle px-3 shadow-sm rounded-pill" onclick="addRow()">
                            <i class="bi bi-plus-circle-fill me-1"></i> Tambah Baris
                        </button>
                    </div>

                    {{-- CONTAINER AKORDION --}}
                    <div class="accordion" id="rowsAccordion">
                        {{-- BARIS PERTAMA (DEFAULT) --}}
                        <div class="accordion-item shadow-sm row-item" id="row_0">
                            <h2 class="accordion-header d-flex align-items-center m-0" id="heading_0">
                                <button class="accordion-button py-3 flex-grow-1" type="button" data-bs-toggle="collapse" data-bs-target="#collapse_0" aria-expanded="true" aria-controls="collapse_0">
                                    <i class="bi bi-list-task me-2"></i> Baris #1
                                </button>
                                <div class="px-3" style="background-color: #f9fafb;">
                                    <button type="button" class="btn btn-outline-danger btn-sm rounded-pill px-3 py-1 delete-row-btn" onclick="event.stopPropagation(); removeRow(this);" style="display: none; font-size: 0.75rem;">
                                        <i class="bi bi-trash-fill"></i> Hapus
                                    </button>
                                </div>
                            </h2>
                            <div id="collapse_0" class="accordion-collapse collapse show" aria-labelledby="heading_0" data-bs-parent="#rowsAccordion">
                                <div class="accordion-body bg-white p-4">
                                    <div class="row g-3">
                                        <div class="col-md-3">
                                            <label class="form-label small fw-bold text-muted mb-1">Tanggal Masuk</label>
                                            <input type="date" name="inputs[0][tgl_masuk]" class="form-control form-control-modern" value="{{ date('Y-m-d') }}" required>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label small fw-bold text-muted mb-1">No HP</label>
                                            <input type="number" name="inputs[0][no_hp]" class="form-control form-control-modern" placeholder="08..." required>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label small fw-bold text-muted mb-1">Metode Penyampaian</label>
                                            <select name="inputs[0][metode_penyampaian]" class="form-select form-select-modern" required>
                                                <option value="">- Pilih Metode -</option>
                                                <option value="Chat">Chat</option>
                                                <option value="Telfon">Telfon</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label small fw-bold text-muted mb-1">Jenis Permintaan</label>
                                            <select name="inputs[0][jenis_permintaan]" class="form-select form-select-modern" required>
                                                <option value="Pengaduan">Pengaduan</option>
                                                <option value="Informasi" selected>Informasi</option>
                                            </select>
                                        </div>

                                        <div class="col-md-4">
                                            <label class="form-label small fw-bold text-muted mb-1">Unit Terkait</label>
                                            <select name="inputs[0][unit_terkait]" class="form-select form-control-modern select2-unit" required>
                                                <option value=""></option>
                                                @foreach($unitDestinations as $unit)
                                                    <option value="{{ $unit->name }}">{{ $unit->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label small fw-bold text-muted mb-1">Tanggal Verifikasi (Opsional)</label>
                                            <input type="date" name="inputs[0][tgl_verifikasi]" class="form-control form-control-modern">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label small fw-bold text-muted mb-1">Uraian</label>
                                            <input type="text" name="inputs[0][uraian]" class="form-control form-control-modern" placeholder="Tulis uraian singkat..." required>
                                        </div>

                                        <div class="col-12 mt-4">
                                            <label class="form-label fw-bold text-primary mb-2"><i class="bi bi-ui-checks-grid me-1"></i> Detail Permintaan (Hanya pilih dalam 1 Kategori):</label>
                                            <div class="border rounded-3 p-3 bg-light" style="max-height: 220px; overflow-y: auto; border-color: #f3f4f6 !important;">
                                                <div class="row g-3 checklist-container">
                                                    @foreach($kategoriPermintaan as $kat)
                                                        <div class="col-md-4">
                                                            <div class="border border-light bg-white rounded p-3 h-100 shadow-sm">
                                                                <div class="fw-bold small text-dark border-bottom pb-2 mb-2">{{ $kat->name }}</div>
                                                                @foreach($kat->items as $sub)
                                                                    <div class="form-check custom-checkbox small mb-2">
                                                                        <input class="form-check-input category-check" type="checkbox" data-category="{{ $kat->name }}" name="inputs[0][detail_keluhan][{{ $kat->name }}][]" value="{{ $sub->name }}" id="c_0_{{ $sub->id }}">
                                                                        <label class="form-check-label text-muted" for="c_0_{{ $sub->id }}">{{ $sub->name }}</label>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer-modern p-3 d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-light shadow-sm btn-modern" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-modern-primary px-4" id="btnSubmit">
                        <i class="bi bi-save me-1"></i> SIMPAN SEMUA DATA
                    </button>
                    <button type="button" class="btn btn-modern-primary px-4 d-none" id="btnLoading" disabled>
                        <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                        Menyimpan...
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Load jQuery dan Select2 JS --}}
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    let rowIndex = 0; 
    const masterUnits = {!! json_encode($unitDestinations) !!};
    const masterKategori = {!! json_encode($kategoriPermintaan) !!};

    $(document).ready(function() {
        // Inisialisasi Select2 untuk form filter
        $('.select2-unit-filter').select2({
            theme: 'bootstrap-5',
            width: '100%',
        });

        // Inisialisasi Select2 untuk baris pertama (default) saat halaman diload
        $('#modalInput').on('shown.bs.modal', function () {
            $('.select2-unit').select2({
                theme: 'bootstrap-5',
                placeholder: "Ketik / Pilih Unit",
                width: '100%',
                dropdownParent: $('#modalInput')
            });
        });
    });

    function addRow() {
        rowIndex++;
        let container = document.getElementById('rowsAccordion');

        // Opsi default Select2
        let optionsUnit = '<option value=""></option>';
        masterUnits.forEach(u => optionsUnit += `<option value="${u.name}">${u.name}</option>`);

        let kategoriHtml = '';
        masterKategori.forEach(kat => {
            let itemsHtml = '';
            kat.items.forEach(sub => {
                itemsHtml += `
                    <div class="form-check custom-checkbox small mb-2">
                        <input class="form-check-input category-check" type="checkbox" data-category="${kat.name}" name="inputs[${rowIndex}][detail_keluhan][${kat.name}][]" value="${sub.name}" id="c_${rowIndex}_${sub.id}">
                        <label class="form-check-label text-muted" for="c_${rowIndex}_${sub.id}">${sub.name}</label>
                    </div>
                `;
            });

            kategoriHtml += `
                <div class="col-md-4">
                    <div class="border border-light bg-white rounded p-3 h-100 shadow-sm">
                        <div class="fw-bold small text-dark border-bottom pb-2 mb-2">${kat.name}</div>
                        ${itemsHtml}
                    </div>
                </div>
            `;
        });

        let newItemHtml = `
            <div class="accordion-item shadow-sm row-item" id="row_${rowIndex}">
                <h2 class="accordion-header d-flex align-items-center m-0" id="heading_${rowIndex}">
                    <button class="accordion-button py-3 flex-grow-1" type="button" data-bs-toggle="collapse" data-bs-target="#collapse_${rowIndex}" aria-expanded="true" aria-controls="collapse_${rowIndex}">
                        <i class="bi bi-list-task me-2"></i> Baris #${document.querySelectorAll('.row-item').length + 1}
                    </button>
                    <div class="px-3" style="background-color: #f9fafb;">
                        <button type="button" class="btn btn-outline-danger btn-sm rounded-pill px-3 py-1 delete-row-btn" onclick="event.stopPropagation(); removeRow(this);" style="font-size: 0.75rem;">
                            <i class="bi bi-trash-fill"></i> Hapus
                        </button>
                    </div>
                </h2>
                <div id="collapse_${rowIndex}" class="accordion-collapse collapse show" aria-labelledby="heading_${rowIndex}" data-bs-parent="#rowsAccordion">
                    <div class="accordion-body bg-white p-4">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label small fw-bold text-muted mb-1">Tanggal Masuk</label>
                                <input type="date" name="inputs[${rowIndex}][tgl_masuk]" class="form-control form-control-modern" value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold text-muted mb-1">No HP</label>
                                <input type="number" name="inputs[${rowIndex}][no_hp]" class="form-control form-control-modern" placeholder="08..." required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold text-muted mb-1">Metode Penyampaian</label>
                                <select name="inputs[${rowIndex}][metode_penyampaian]" class="form-select form-select-modern" required>
                                    <option value="">- Pilih Metode -</option>
                                    <option value="Chat">Chat</option>
                                    <option value="Telfon">Telfon</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold text-muted mb-1">Jenis Permintaan</label>
                                <select name="inputs[${rowIndex}][jenis_permintaan]" class="form-select form-select-modern" required>
                                    <option value="Pengaduan">Pengaduan</option>
                                    <option value="Informasi" selected>Informasi</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-muted mb-1">Unit Terkait</label>
                                <select name="inputs[${rowIndex}][unit_terkait]" class="form-select form-control-modern select2-unit" required>${optionsUnit}</select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-muted mb-1">Tanggal Verifikasi (Opsional)</label>
                                <input type="date" name="inputs[${rowIndex}][tgl_verifikasi]" class="form-control form-control-modern">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-muted mb-1">Uraian</label>
                                <input type="text" name="inputs[${rowIndex}][uraian]" class="form-control form-control-modern" placeholder="Tulis uraian singkat..." required>
                            </div>

                            <div class="col-12 mt-4">
                                <label class="form-label fw-bold text-primary mb-2"><i class="bi bi-ui-checks-grid me-1"></i> Detail Permintaan (Hanya pilih dalam 1 Kategori):</label>
                                <div class="border rounded-3 p-3 bg-light" style="max-height: 220px; overflow-y: auto; border-color: #f3f4f6 !important;">
                                    <div class="row g-3 checklist-container">
                                        ${kategoriHtml}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        container.insertAdjacentHTML('beforeend', newItemHtml);
        updateRowNumbers();

        // Inisialisasi Select2 untuk baris yang baru
        $(`#row_${rowIndex} .select2-unit`).select2({
            theme: 'bootstrap-5',
            placeholder: "Ketik / Pilih Unit",
            width: '100%',
            dropdownParent: $('#modalInput')
        });
    }

    function removeRow(btn) {
        let items = document.querySelectorAll('.row-item');
        if (items.length <= 1) {
            alert('Minimal harus ada 1 baris data!');
            return;
        }
        let card = btn.closest('.row-item');
        card.remove();
        updateRowNumbers();
    }

    function updateRowNumbers() {
        let items = document.querySelectorAll('.row-item');
        items.forEach((item, index) => {
            let btn = item.querySelector('.accordion-button');
            if (btn) {
                btn.innerHTML = `<i class="bi bi-list-task me-2"></i> Baris #${index + 1}`;
            }
            let delBtn = item.querySelector('.delete-row-btn');
            if (delBtn) {
                delBtn.style.display = items.length > 1 ? 'inline-block' : 'none';
            }
        });
    }

    document.addEventListener("DOMContentLoaded", function() {
        updateRowNumbers();
    });

    function showLoading() {
        document.getElementById('btnSubmit').classList.add('d-none');
        document.getElementById('btnLoading').classList.remove('d-none');
    }

    // --- SCRIPT KUNCI KATEGORI PERMINTAAN ---
    document.addEventListener("DOMContentLoaded", function() {
        function applyCategoryLock(container) {
            let checkedBoxes = container.querySelectorAll('.category-check:checked');
            let selectedCategory = checkedBoxes.length > 0 ? checkedBoxes[0].getAttribute('data-category') : null;

            container.querySelectorAll('.category-check').forEach(box => {
                if (selectedCategory && box.getAttribute('data-category') !== selectedCategory) {
                    box.disabled = true;
                } else {
                    box.disabled = false;
                }
            });
        }

        // Event delegation untuk checkbox dinamis
        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('category-check')) {
                let container = e.target.closest('.checklist-container');
                if (container) applyCategoryLock(container);
            }
        });

        // Lock on load (Untuk Modal Edit)
        document.querySelectorAll('.checklist-container').forEach(container => {
            applyCategoryLock(container);
        });
    });
</script>

@endsection