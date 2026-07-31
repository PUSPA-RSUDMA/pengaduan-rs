@extends('layouts.admin')

@section('title', 'Data Layanan Informasi')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="m-0 font-weight-bold text-primary"><i class="bi bi-table me-2"></i>Daftar Layanan Informasi</h5>
            
            <button type="button" class="btn btn-primary btn-sm fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalInput">    
                <i class="bi bi-plus-lg me-1"></i> Buat Baru
            </button>
        </div>

        {{-- BAGIAN FORM FILTER --}}
        <div class="bg-light p-3 rounded border">
            <form action="{{ route('permintaan.index') }}" method="GET">
                <div class="row g-2 align-items-center">
                    
                    {{-- 1. FILTER TANGGAL --}}
                    <div class="col-12 col-lg-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-white border-end-0"><i class="bi bi-calendar3"></i></span>
                            <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}" title="Mulai">
                            <span class="input-group-text bg-light border-start-0 border-end-0">s/d</span>
                            <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}" title="Sampai">
                        </div>
                    </div>

                    {{-- 2. UNIT TERKAIT --}}
                    <div class="col-6 col-lg-auto">
                        <select name="unit_terkait" class="form-select form-select-sm" onchange="this.form.submit()" style="min-width: 140px;">
                            <option value="">- Semua Unit -</option>
                            @foreach($unitDestinations as $unit)
                                <option value="{{ $unit->name }}" {{ request('unit_terkait') == $unit->name ? 'selected' : '' }}>
                                    {{ $unit->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- 3. SEARCH --}}
                    <div class="col-6 col-lg-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                            <input type="text" name="search" class="form-control" placeholder="Cari No HP / Uraian..." value="{{ request('search') }}">
                        </div>
                    </div>

                    {{-- 4. SORT --}}
                    <div class="col-6 col-lg-auto">
                        <select name="sort" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="terbaru" {{ request('sort') == 'terbaru' ? 'selected' : '' }}>⬇️ Terbaru</option>
                            <option value="terlama" {{ request('sort') == 'terlama' ? 'selected' : '' }}>⬆️ Terlama</option>
                        </select>
                    </div>

                    {{-- 5. TOMBOL FILTER --}}
                    <div class="col-auto d-flex gap-1">
                        <button type="submit" class="btn btn-secondary btn-sm" title="Terapkan Filter">
                            <i class="bi bi-filter"></i>
                        </button>
                        
                        <a href="{{ route('permintaan.export.pdf', request()->query()) }}" class="btn btn-danger btn-sm text-white" target="_blank" title="Download PDF">
                            <i class="bi bi-file-pdf"></i>
                        </a>

                        <a href="{{ route('permintaan.export.excel', request()->query()) }}" class="btn btn-success btn-sm" title="Download Excel">
                            <i class="bi bi-file-excel"></i>
                        </a>

                        <a href="{{ route('permintaan.index') }}" class="btn btn-outline-danger btn-sm" title="Reset Filter">
                            <i class="bi bi-x-lg"></i>
                        </a>
                    </div>

                </div>
            </form>
        </div>
    </div>
    
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-3">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle" style="font-size: 0.85rem;">
                <thead class="table-light text-center text-uppercase align-middle">
                    <tr>
                        <th width="5%">No</th>
                        <th>Tgl Masuk</th>
                        <th>No HP</th>
                        <th>Metode</th>
                        <th>Jenis Permintaan</th>
                        <th width="20%">Detail Keluhan</th>
                        <th width="25%">Uraian</th>
                        <th>Unit Terkait</th>
                        <th>Tgl Verifikasi</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($permintaans as $item)
                    <tr>
                        <td class="text-center">{{ $loop->iteration + ($permintaans->currentPage() - 1) * $permintaans->perPage() }}</td>
                        <td class="text-center fw-bold text-primary">{{ \Carbon\Carbon::parse($item->tgl_masuk)->format('d/m/Y') }}</td>
                        <td class="fw-bold">{{ $item->no_hp }}</td>
                        <td class="text-center">
                            @if($item->metode_penyampaian == 'Chat')
                                <span class="badge bg-success"><i class="bi bi-whatsapp me-1"></i> Chat</span>
                            @else
                                <span class="badge bg-info text-dark"><i class="bi bi-telephone-fill me-1"></i> Telfon</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($item->jenis_permintaan == 'Pengaduan')
                                <span class="badge bg-danger">Pengaduan</span>
                            @else
                                <span class="badge bg-primary">Informasi</span>
                            @endif
                        </td>

                        {{-- KOLOM DETAIL KELUHAN DINAMIS --}}
                        <td class="text-center">
                            <div class="mb-1 d-flex flex-wrap justify-content-center gap-1">
                                @if(!empty($item->detail_keluhan) && is_array($item->detail_keluhan))
                                    @foreach($item->detail_keluhan as $catName => $subItems)
                                        <span class="badge bg-secondary" style="font-size: 0.65rem;">{{ $catName }}</span>
                                    @endforeach
                                @else
                                    <span class="text-muted fst-italic" style="font-size: 0.7rem;">-</span>
                                @endif
                            </div>
                            <button class="btn btn-outline-info btn-sm rounded-pill py-0 px-2 fw-bold" style="font-size: 0.75rem;" data-bs-toggle="modal" data-bs-target="#modalDetail{{ $item->id }}">
                                <i class="bi bi-search me-1"></i> Lihat
                            </button>
                        </td>

                        <td>{{ Str::limit($item->uraian, 80) }}</td>
                        <td class="fw-bold">{{ $item->unit_terkait }}</td>
                        <td class="text-center">
                            @if($item->tgl_verifikasi)
                                <span class="text-success fw-bold"><i class="bi bi-check-circle me-1"></i> {{ \Carbon\Carbon::parse($item->tgl_verifikasi)->format('d/m/Y') }}</span>
                            @else
                                <span class="badge bg-secondary">Belum</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <button type="button" class="btn btn-warning text-white" data-bs-toggle="modal" data-bs-target="#modalEdit{{ $item->id }}" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <form action="{{ route('permintaan.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus data ini?');" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-danger" title="Hapus"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>

                    {{-- MODAL LIHAT DETAIL KELUHAN --}}
                    <div class="modal fade" id="modalDetail{{ $item->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-centered">
                            <div class="modal-content border-0 shadow-lg">
                                <div class="modal-header bg-info text-white py-2">
                                    <h6 class="modal-title fw-bold"><i class="bi bi-card-text me-2"></i>Rincian Detail Keluhan</h6>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body p-4 bg-light">
                                    <div class="row g-3">
                                        @if(!empty($item->detail_keluhan) && is_array($item->detail_keluhan))
                                            @foreach($item->detail_keluhan as $catName => $subItems)
                                            <div class="col-md-6">
                                                <div class="card border-0 shadow-sm h-100">
                                                    <div class="card-header bg-white fw-bold text-dark py-1 small">{{ $catName }}</div>
                                                    <div class="card-body p-2 small">
                                                        <ul class="mb-0 ps-3">
                                                            @foreach($subItems as $subItem) 
                                                                <li>{{ $subItem }}</li> 
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                        @else
                                            <div class="col-12"><p class="text-muted fst-italic">Tidak ada kategori detail yang dipilih.</p></div>
                                        @endif
                                    </div>
                                </div>
                                <div class="modal-footer py-2 bg-white border-top">
                                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- END MODAL DETAIL --}}

                    {{-- MODAL EDIT --}}
                    <div class="modal fade" id="modalEdit{{ $item->id }}" tabindex="-1" data-bs-backdrop="static">
                        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                            <div class="modal-content border-0 shadow-lg">
                                <div class="modal-header bg-warning text-dark py-2">
                                    <h6 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2"></i>Edit Data Layanan & Informasi</h6>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <form action="{{ route('permintaan.update', $item->id) }}" method="POST">
                                    @csrf @method('PUT')
                                    <div class="modal-body p-4 bg-light">
                                        <div class="row g-3 mb-3">
                                            <div class="col-md-3">
                                                <label class="small fw-bold text-muted">Tanggal Masuk</label>
                                                <input type="date" name="tgl_masuk" class="form-control form-control-sm" value="{{ \Carbon\Carbon::parse($item->tgl_masuk)->format('Y-m-d') }}" required>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="small fw-bold text-muted">No HP</label>
                                                <input type="number" name="no_hp" class="form-control form-control-sm" value="{{ $item->no_hp }}" required>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="small fw-bold text-muted">Metode Penyampaian</label>
                                                <select name="metode_penyampaian" class="form-select form-select-sm" required>
                                                    <option value="Chat" {{ $item->metode_penyampaian == 'Chat' ? 'selected' : '' }}>Chat</option>
                                                    <option value="Telfon" {{ $item->metode_penyampaian == 'Telfon' ? 'selected' : '' }}>Telfon</option>
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="small fw-bold text-muted">Jenis Permintaan</label>
                                                <select name="jenis_permintaan" class="form-select form-select-sm fw-bold border-warning" required>
                                                    <option value="Pengaduan" {{ $item->jenis_permintaan == 'Pengaduan' ? 'selected' : '' }}>Pengaduan</option>
                                                    <option value="Informasi" {{ $item->jenis_permintaan == 'Informasi' ? 'selected' : '' }}>Informasi</option>
                                                </select>
                                            </div>
                                        </div>

                                        {{-- CHECKLIST DETAIL KELUHAN DINAMIS --}}
                                        <h6 class="fw-bold text-primary mb-3 border-bottom pb-2">Detail Keluhan (Master Checklist)</h6>
                                        <div class="row g-3 mb-3">
                                            @php 
                                                $savedDetails = is_array($item->detail_keluhan) ? $item->detail_keluhan : []; 
                                            @endphp
                                            @foreach($kategoriKeluhan as $kategori)
                                            <div class="col-md-4">
                                                <div class="card h-100 border-0 shadow-sm">
                                                    <div class="card-header bg-white fw-bold text-primary py-2 small">{{ $kategori->name }}</div>
                                                    <div class="card-body small p-2">
                                                        @foreach($kategori->items as $subItem)
                                                        <div class="form-check mb-1">
                                                            <input class="form-check-input" type="checkbox" 
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

                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="small fw-bold text-muted">Uraian</label>
                                                <textarea name="uraian" class="form-control form-control-sm" rows="3" required>{{ $item->uraian }}</textarea>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="small fw-bold text-muted">Unit Terkait</label>
                                                <select name="unit_terkait" class="form-select form-select-sm" required>
                                                    @foreach($unitDestinations as $unit)
                                                        <option value="{{ $unit->name }}" {{ $item->unit_terkait == $unit->name ? 'selected' : '' }}>{{ $unit->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="small fw-bold text-muted">Tanggal Verifikasi</label>
                                                <input type="date" name="tgl_verifikasi" class="form-control form-control-sm border-success" value="{{ $item->tgl_verifikasi ? \Carbon\Carbon::parse($item->tgl_verifikasi)->format('Y-m-d') : '' }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer bg-white border-top py-2">
                                        <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-warning btn-sm fw-bold">Update Data</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    {{-- END MODAL EDIT --}}

                    @empty
                    <tr><td colspan="10" class="text-center py-4 text-muted">Data tidak ditemukan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3 d-flex justify-content-end">
            {{ $permintaans->links() }}
        </div>
    </div>
</div>

{{-- MODAL INPUT BARU (MULTI-ROW BERBASIS KARTU YANG LUAS & MUDAH DIOPERASIKAN) --}}
<div class="modal fade" id="modalInput" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg">
            
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-journal-plus me-2"></i>Input Data Baru (Multi-Row)
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            
            <form action="{{ route('permintaan.store') }}" method="POST" id="formSimpan" onsubmit="showLoading()">
                @csrf
                
                <div class="modal-body p-4 bg-light">
                    
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <small class="text-muted fst-italic">* Anda dapat menambahkan beberapa baris data sekaligus sebelum disimpan.</small>
                        <button type="button" class="btn btn-success btn-sm fw-bold shadow-sm" onclick="addRow()">
                            <i class="bi bi-plus-circle-fill me-1"></i> Tambah Baris Baru
                        </button>
                    </div>

                    {{-- CONTAINER UNTUK MENAMPUNG KARTU BARIS INPUT --}}
                    <div id="rowsContainer">
                        {{-- BARIS PERTAMA (DEFAULT) --}}
                        <div class="card border mb-3 shadow-sm row-item" id="row_0">
                            <div class="card-header bg-white d-flex justify-content-between align-items-center py-2 border-bottom">
                                <span class="fw-bold text-primary"><i class="bi bi-pencil-square me-1"></i> Baris #1</span>
                                <button type="button" class="btn btn-outline-danger btn-sm px-2 py-0" disabled>
                                    <i class="bi bi-trash"></i> Hapus
                                </button>
                            </div>
                            <div class="card-body bg-white">
                                <div class="row g-3">
                                    {{-- Data Utama --}}
                                    <div class="col-md-3">
                                        <label class="form-label small fw-bold">Tanggal Masuk</label>
                                        <input type="date" name="inputs[0][tgl_masuk]" class="form-control form-control-sm" value="{{ date('Y-m-d') }}" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small fw-bold">No HP</label>
                                        <input type="number" name="inputs[0][no_hp]" class="form-control form-control-sm" placeholder="08..." required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small fw-bold">Metode Penyampaian</label>
                                        <select name="inputs[0][metode_penyampaian]" class="form-select form-select-sm" required>
                                            <option value="">- Pilih Metode -</option>
                                            <option value="Chat">Chat</option>
                                            <option value="Telfon">Telfon</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small fw-bold">Jenis Permintaan</label>
                                        <select name="inputs[0][jenis_permintaan]" class="form-select form-select-sm" required>
                                            <option value="">- Pilih Jenis -</option>
                                            <option value="Pengaduan">Pengaduan</option>
                                            <option value="Informasi">Informasi</option>
                                        </select>
                                    </div>

                                    {{-- Unit, Verifikasi, Uraian --}}
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold">Unit Terkait</label>
                                        <select name="inputs[0][unit_terkait]" class="form-select form-select-sm" required>
                                            <option value="">- Pilih Unit -</option>
                                            @foreach($unitDestinations as $unit)
                                                <option value="{{ $unit->name }}">{{ $unit->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold">Tanggal Verifikasi (Opsional)</label>
                                        <input type="date" name="inputs[0][tgl_verifikasi]" class="form-control form-control-sm">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold">Uraian</label>
                                        <input type="text" name="inputs[0][uraian]" class="form-control form-control-sm" placeholder="Tulis uraian singkat..." required>
                                    </div>

                                    {{-- DETAIL KELUHAN (CHECKLIST BERSTRUKTUR GRID) --}}
                                    <div class="col-12">
                                        <label class="form-label small fw-bold text-dark"><i class="bi bi-ui-checks-grid me-1 text-primary"></i> Detail Keluhan (Pilih yang sesuai):</label>
                                        <div class="border rounded p-3 bg-light" style="max-height: 220px; overflow-y: auto;">
                                            <div class="row g-3">
                                                @foreach($kategoriKeluhan as $kat)
                                                    <div class="col-md-4">
                                                        <div class="border bg-white rounded p-2 h-100 shadow-sm">
                                                            <div class="fw-bold small text-primary border-bottom pb-1 mb-1">{{ $kat->name }}</div>
                                                            @foreach($kat->items as $sub)
                                                                <div class="form-check small mb-1">
                                                                    <input class="form-check-input" type="checkbox" name="inputs[0][detail_keluhan][{{ $kat->name }}][]" value="{{ $sub->name }}" id="c_0_{{ $sub->id }}">
                                                                    <label class="form-check-label" for="c_0_{{ $sub->id }}">{{ $sub->name }}</label>
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

                <div class="modal-footer bg-white border-top shadow-sm py-2">
                    <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm fw-bold px-4" id="btnSubmit">
                        <i class="bi bi-save me-1"></i> SIMPAN SEMUA DATA
                    </button>
                    <button type="button" class="btn btn-primary btn-sm fw-bold px-4 d-none" id="btnLoading" disabled>
                        <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                        Menyimpan...
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    let i = 0; 
    const masterUnits = {!! json_encode($unitDestinations) !!};
    const masterKategori = {!! json_encode($kategoriKeluhan) !!};

    function addRow() {
        ++i;
        let container = document.getElementById('rowsContainer');
        
        let optionsUnit = '<option value="">- Pilih Unit -</option>';
        masterUnits.forEach(u => optionsUnit += `<option value="${u.name}">${u.name}</option>`);

        let kategoriHtml = '';
        masterKategori.forEach(kat => {
            let itemsHtml = '';
            kat.items.forEach(sub => {
                itemsHtml += `
                    <div class="form-check small mb-1">
                        <input class="form-check-input" type="checkbox" name="inputs[${i}][detail_keluhan][${kat.name}][]" value="${sub.name}" id="c_${i}_${sub.id}">
                        <label class="form-check-label" for="c_${i}_${sub.id}">${sub.name}</label>
                    </div>
                `;
            });

            kategoriHtml += `
                <div class="col-md-4">
                    <div class="border bg-white rounded p-2 h-100 shadow-sm">
                        <div class="fw-bold small text-primary border-bottom pb-1 mb-1">${kat.name}</div>
                        ${itemsHtml}
                    </div>
                </div>
            `;
        });

        let cardHtml = `
            <div class="card border mb-3 shadow-sm row-item" id="row_${i}">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-2 border-bottom">
                    <span class="fw-bold text-primary"><i class="bi bi-pencil-square me-1"></i> Baris #${i + 1}</span>
                    <button type="button" class="btn btn-outline-danger btn-sm px-2 py-0" onclick="removeRow(this)">
                        <i class="bi bi-trash"></i> Hapus
                    </button>
                </div>
                <div class="card-body bg-white">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Tanggal Masuk</label>
                            <input type="date" name="inputs[${i}][tgl_masuk]" class="form-control form-control-sm" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">No HP</label>
                            <input type="number" name="inputs[${i}][no_hp]" class="form-control form-control-sm" placeholder="08..." required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Metode Penyampaian</label>
                            <select name="inputs[${i}][metode_penyampaian]" class="form-select form-select-sm" required>
                                <option value="">- Pilih Metode -</option>
                                <option value="Chat">Chat</option>
                                <option value="Telfon">Telfon</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Jenis Permintaan</label>
                            <select name="inputs[${i}][jenis_permintaan]" class="form-select form-select-sm" required>
                                <option value="">- Pilih Jenis -</option>
                                <option value="Pengaduan">Pengaduan</option>
                                <option value="Informasi">Informasi</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Unit Terkait</label>
                            <select name="inputs[${i}][unit_terkait]" class="form-select form-select-sm" required>${optionsUnit}</select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Tanggal Verifikasi (Opsional)</label>
                            <input type="date" name="inputs[${i}][tgl_verifikasi]" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Uraian</label>
                            <input type="text" name="inputs[${i}][uraian]" class="form-control form-control-sm" placeholder="Tulis uraian singkat..." required>
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-bold text-dark"><i class="bi bi-ui-checks-grid me-1 text-primary"></i> Detail Keluhan (Pilih yang sesuai):</label>
                            <div class="border rounded p-3 bg-light" style="max-height: 220px; overflow-y: auto;">
                                <div class="row g-3">
                                    ${kategoriHtml}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        container.insertAdjacentHTML('beforeend', cardHtml);
    }

    function removeRow(btn) {
        let card = btn.closest('.row-item');
        card.remove();
    }

    function showLoading() {
        document.getElementById('btnSubmit').classList.add('d-none');
        document.getElementById('btnLoading').classList.remove('d-none');
    }
</script>

@endsection