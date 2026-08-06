@extends('layouts.admin')

@section('title', 'Data Keluhan')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="m-0 font-weight-bold text-primary"><i class="bi bi-table me-2"></i>Daftar Pengaduan</h5>
            
            @if(auth()->user()->role !== 'relationship')
            <button type="button" class="btn btn-primary btn-sm fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalInput">    
                <i class="bi bi-plus-lg me-1"></i> Buat Baru
            </button>
            @endif
        </div>

        {{-- BAGIAN FORM FILTER --}}
        <div class="bg-light p-3 rounded border">
            <form action="{{ route('complaints.index') }}" method="GET">
                <div class="row g-2 align-items-center">
                    
                    <div class="col-12 col-lg-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-white border-end-0"><i class="bi bi-calendar3"></i></span>
                            <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}" title="Mulai">
                            <span class="input-group-text bg-light border-start-0 border-end-0">s/d</span>
                            <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}" title="Sampai">
                        </div>
                    </div>

                    <div class="col-6 col-lg-auto">
                        <select name="unit_destination" class="form-select form-select-sm" onchange="this.form.submit()" style="min-width: 140px;">
                            <option value="">- Semua Unit -</option>
                            @foreach($unitDestinations as $unit)
                                <option value="{{ $unit->name }}" {{ request('unit_destination') == $unit->name ? 'selected' : '' }}>
                                    {{ $unit->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-6 col-lg-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                            <input type="text" name="search" class="form-control" placeholder="Cari..." value="{{ request('search') }}">
                        </div>
                    </div>

                    <div class="col-6 col-lg-auto">
                        <select name="sort" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="terbaru" {{ request('sort') == 'terbaru' ? 'selected' : '' }}>⬇️ Terbaru</option>
                            <option value="terlama" {{ request('sort') == 'terlama' ? 'selected' : '' }}>⬆️ Terlama</option>
                        </select>
                    </div>

                    <div class="col-auto d-flex gap-1">
                        <button type="submit" class="btn btn-secondary btn-sm" title="Terapkan Filter"><i class="bi bi-filter"></i></button>
                        <button type="button" class="btn btn-success btn-sm text-white" data-bs-toggle="modal" data-bs-target="#modalImport" title="Import Excel">
                            <i class="bi bi-file-earmark-spreadsheet"></i> <span class="d-none d-xl-inline">Import</span>
                        </button>
                        <a href="{{ route('export.pdf', request()->query()) }}" class="btn btn-danger btn-sm text-white" target="_blank" title="Download PDF"><i class="bi bi-file-pdf"></i></a>
                        <a href="{{ route('export.excel', request()->query()) }}" class="btn btn-success btn-sm" title="Download Excel"><i class="bi bi-file-excel"></i></a>
                        <a href="{{ route('complaints.index') }}" class="btn btn-outline-danger btn-sm" title="Reset Filter"><i class="bi bi-x-lg"></i></a>
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

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show mb-3">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle" style="font-size: 0.85rem;">
                <thead class="table-light text-center text-uppercase align-middle">
                    <tr>
                        <th width="5%">No</th>
                        <th width="10%">Tgl Masuk</th>
                        <th width="15%">Unit Pelapor</th>
                        <th width="10%">Media</th>
                        <th width="5%">Grade</th>
                        <th width="20%">Isi Keluhan & Detail</th>
                        <th width="15%">Unit Tujuan</th>
                        <th width="10%">Status</th>
                        <th width="10%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($complaints as $complaint)
                    <tr>
                        <td class="text-center">{{ $loop->iteration + ($complaints->currentPage() - 1) * $complaints->perPage() }}</td>
                        <td class="text-center fw-bold text-primary">{{ \Carbon\Carbon::parse($complaint->date)->format('d/m/Y') }}</td>
                        <td>
                            <span class="fw-bold">{{ $complaint->reporter_type }}</span>
                            @if($complaint->reporter_name)
                                <br><small class="text-muted">({{ $complaint->reporter_name }})</small>
                            @endif
                        </td>
                        <td class="text-center">{{ $complaint->source->name ?? '-' }}</td>
                        
                        {{-- GRADE --}}
                        <td class="text-center">
                            @php
                                $isHex = Str::startsWith($complaint->grade, '#'); 
                                $colorClass = 'bg-secondary';
                                if(!$isHex) {
                                    if(str_contains(strtolower($complaint->grade), 'merah')) $colorClass = 'bg-danger';
                                    elseif(str_contains(strtolower($complaint->grade), 'kuning')) $colorClass = 'bg-warning border border-white';
                                    elseif(str_contains(strtolower($complaint->grade), 'hijau')) $colorClass = 'bg-success';
                                }
                            @endphp
                            
                            @if($isHex)
                                <span class="badge rounded-circle border border-white p-2 shadow-sm" 
                                      style="background-color: {{ $complaint->grade }}; width: 15px; height: 15px; display: inline-block;" title="{{ $complaint->grade }}"> </span>
                            @else
                                <span class="badge rounded-circle {{ $colorClass }} border border-white p-2" title="{{ $complaint->grade }}"> </span>
                            @endif
                        </td>

                        {{-- KOLOM DETAIL KELUHAN DINAMIS (DARI DATABASE) --}}
                        <td class="text-center">
                            <div class="mb-2 d-flex flex-wrap justify-content-center gap-1">
                                @if(!empty($complaint->detail_keluhan) && is_array($complaint->detail_keluhan))
                                    @foreach($complaint->detail_keluhan as $catName => $items)
                                        <span class="badge bg-secondary" style="font-size: 0.7rem;">{{ $catName }}</span>
                                    @endforeach
                                @else
                                    <span class="text-muted fst-italic" style="font-size: 0.7rem;">Lainnya</span>
                                @endif
                            </div>
                            
                            {{-- Tombol Lihat Detail --}}
                            <button class="btn btn-outline-info btn-sm rounded-pill fw-bold" data-bs-toggle="modal" data-bs-target="#modalDetail{{ $complaint->id }}">
                                <i class="bi bi-search me-1"></i> Lihat Lengkap
                            </button>
                        </td>

                        <td class="text-center fw-bold">{{ $complaint->unit_destination }}</td>
                        
                        <td class="text-center">
                            @if($complaint->status == 'Pending')
                                <span class="badge bg-secondary">Pending</span>
                            @elseif($complaint->status == 'Proses')
                                <span class="badge bg-primary">Proses</span>
                            @else
                                <span class="badge bg-success">Selesai</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <button type="button" class="btn btn-warning text-white" data-bs-toggle="modal" data-bs-target="#modalEdit{{ $complaint->id }}" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                
                                @if(auth()->user()->role == 'admin')
                                    <form action="{{ route('complaints.destroy', $complaint->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus data ini?');" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-danger" title="Hapus"><i class="bi bi-trash"></i></button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>

                    {{-- MODAL LIHAT DETAIL KELUHAN (DINAMIS DARI DATABASE) --}}
                    <div class="modal fade" id="modalDetail{{ $complaint->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-centered">
                            <div class="modal-content border-0 shadow-lg">
                                <div class="modal-header bg-info text-white py-2">
                                    <h6 class="modal-title fw-bold"><i class="bi bi-card-text me-2"></i>Rincian Pengaduan Lengkap</h6>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body p-4 bg-light">
                                    
                                    <h6 class="fw-bold text-primary border-bottom pb-2 mb-3">Item yang Dikeluhkan:</h6>
                                    <div class="row g-3">
                                        @if(!empty($complaint->detail_keluhan) && is_array($complaint->detail_keluhan))
                                            @foreach($complaint->detail_keluhan as $catName => $items)
                                            <div class="col-md-6">
                                                <div class="card border-0 shadow-sm h-100">
                                                    <div class="card-header bg-white fw-bold text-dark py-1 small">{{ $catName }}</div>
                                                    <div class="card-body p-2 small">
                                                        <ul class="mb-0 ps-3">
                                                            @foreach($items as $item) 
                                                                <li>{{ $item }}</li> 
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                        @else
                                            <div class="col-12"><p class="text-muted fst-italic">Tidak ada item kategori (checklist) yang dipilih.</p></div>
                                        @endif
                                    </div>

                                    <div class="mt-4">
                                        <h6 class="fw-bold text-dark border-bottom pb-1">Deskripsi Tambahan (Lainnya):</h6>
                                        <div class="p-2 bg-white rounded border border-light small" style="white-space: pre-line;">
                                            {{ $complaint->description ?: 'Tidak ada deskripsi tambahan.' }}
                                        </div>
                                    </div>

                                    <div class="mt-3">
                                        <h6 class="fw-bold text-success border-bottom pb-1">Tindak Lanjut / Solusi:</h6>
                                        <div class="p-2 bg-white rounded border border-light small" style="white-space: pre-line;">
                                            {{ $complaint->answer ?: 'Belum ada tindak lanjut.' }}
                                        </div>
                                    </div>

                                </div>
                                <div class="modal-footer py-2 bg-white border-top">
                                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- END MODAL DETAIL --}}

                    {{-- MODAL EDIT (MENGGUNAKAN MASTER KATEGORI DINAMIS) --}}
                    <div class="modal fade" id="modalEdit{{ $complaint->id }}" tabindex="-1" data-bs-backdrop="static">
                        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                            <div class="modal-content border-0 shadow-lg">
                                <div class="modal-header bg-warning text-dark py-2">
                                    <h6 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2"></i>Edit Pengaduan</h6>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <form action="{{ route('complaints.update', $complaint->id) }}" method="POST">
                                    @csrf @method('PUT')
                                    <div class="modal-body p-4 bg-light">
                                        <div class="row g-3 mb-4">
                                            <div class="col-md-3">
                                                <label class="small fw-bold text-muted">Tanggal</label>
                                                <input type="date" name="date" class="form-control form-control-sm" value="{{ \Carbon\Carbon::parse($complaint->date)->format('Y-m-d') }}" required>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="small fw-bold text-muted">Unit Pelapor</label>
                                                <select name="reporter_type" class="form-select form-select-sm" required>
                                                    @foreach($reporterTypes as $type)
                                                        <option value="{{ $type->name }}" {{ $complaint->reporter_type == $type->name ? 'selected' : '' }}>{{ $type->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="small fw-bold text-muted">Media</label>
                                                <select name="source_id" class="form-select form-select-sm" required>
                                                    @foreach($sources as $source)
                                                        <option value="{{ $source->id }}" {{ $complaint->source_id == $source->id ? 'selected' : '' }}>{{ $source->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="small fw-bold text-muted">Nama Pelapor</label>
                                                <input type="text" name="reporter_name" class="form-control form-control-sm" value="{{ $complaint->reporter_name }}">
                                            </div>
                                        </div>

                                        <h6 class="fw-bold text-primary mb-3 border-bottom pb-2">Detail Keluhan (Dinamic Checklist)</h6>
                                        <div class="row g-3">
                                            @php 
                                                // Ambil JSON yang tersimpan, pastikan berupa array
                                                $savedDetails = is_array($complaint->detail_keluhan) ? $complaint->detail_keluhan : []; 
                                            @endphp
                                            
                                            {{-- LOOPING MASTER KATEGORI --}}
                                            @foreach($kategoriKeluhan as $kategori)
                                            <div class="col-md-4">
                                                <div class="card h-100 border-0 shadow-sm">
                                                    <div class="card-header bg-white fw-bold text-primary py-2 small">{{ $kategori->name }}</div>
                                                    <div class="card-body small p-2">
                                                        @foreach($kategori->items as $item)
                                                        <div class="form-check mb-1">
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

                                        <div class="row g-3 mt-1">
                                            <div class="col-md-6">
                                                <label class="small fw-bold text-muted">Deskripsi Tambahan / Lain-lain</label>
                                                <textarea name="description" class="form-control form-control-sm" rows="3">{{ $complaint->description }}</textarea>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="small fw-bold text-muted">Tindak Lanjut / Solusi</label>
                                                <textarea name="answer" class="form-control form-control-sm" rows="3">{{ $complaint->answer }}</textarea>
                                            </div>
                                        </div>

                                        <div class="row g-3 mt-1">
                                            <div class="col-md-4">
                                                <label class="small fw-bold text-muted">Unit Tujuan</label>
                                                <select name="unit_destination" class="form-select form-select-sm" required>
                                                    @foreach($unitDestinations as $unit)
                                                        <option value="{{ $unit->name }}" {{ $complaint->unit_destination == $unit->name ? 'selected' : '' }}>{{ $unit->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="small fw-bold text-muted">Tingkat Kegawatan</label>
                                                <select name="grade" class="form-select form-select-sm fw-bold">
                                                    @foreach($grades as $grade)
                                                        <option value="{{ $grade->color_class ?? $grade->name }}" {{ $complaint->grade == ($grade->color_class ?? $grade->name) ? 'selected' : '' }}>
                                                            {{ $grade->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="small fw-bold text-muted">Status</label>
                                                <select name="status" class="form-select form-select-sm fw-bold">
                                                    <option value="Pending" {{ $complaint->status == 'Pending' ? 'selected' : '' }}>⏳ Pending</option>
                                                    <option value="Proses" {{ $complaint->status == 'Proses' ? 'selected' : '' }}>🔄 Proses</option>
                                                    <option value="Selesai" {{ $complaint->status == 'Selesai' ? 'selected' : '' }}>✅ Selesai</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer bg-white border-top py-1">
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
            {{ $complaints->links() }}
        </div>
    </div>
</div>

{{-- MODAL IMPORT EXCEL --}}
<div class="modal fade" id="modalImport" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title fw-bold"><i class="bi bi-file-earmark-excel me-2"></i>Import Data Excel</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('complaints.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-4 text-center border-bottom pb-4">
                        <p class="fw-bold mb-2">Belum punya format Excel-nya?</p>
                        <a href="{{ asset('template_pengaduan.xlsx') }}" class="btn btn-outline-success w-100 fw-bold border-2 shadow-sm" download>
                            <i class="bi bi-download me-2"></i> Download Template Excel
                        </a>
                        <small class="text-muted d-block mt-2 fst-italic">*Silakan download, isi data, lalu upload kembali file tersebut.</small>
                    </div>
                    <div class="mb-2">
                        <label class="form-label fw-bold">Upload File Excel (.xlsx / .xls)</label>
                        <input type="file" name="file" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success fw-bold"><i class="bi bi-cloud-upload-fill me-1"></i> Upload & Import</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL INPUT BARU (MENGGUNAKAN MASTER KATEGORI DINAMIS) --}}
<div class="modal fade" id="modalInput" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white py-2">
                <h5 class="modal-title fw-bold"><i class="bi bi-ui-checks-grid me-2"></i>Input Laporan Baru (Checklist)</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            
            <form action="{{ route('complaints.store') }}" method="POST" id="formSimpan" onsubmit="showLoading()">
                @csrf
                <div class="modal-body p-4 bg-light">
                    {{-- INFO IDENTITAS --}}
                    <div class="row g-3 mb-4">
                        <div class="col-md-3">
                            <label class="small fw-bold">Tanggal <span class="text-danger">*</span></label>
                            <input type="date" name="date" class="form-control form-control-sm" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="small fw-bold">Unit Pelapor <span class="text-danger">*</span></label>
                            <select name="reporter_type" class="form-select form-select-sm" required>
                                <option value="">- Pilih -</option>
                                @foreach($reporterTypes as $type) <option value="{{ $type->name }}">{{ $type->name }}</option> @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="small fw-bold">Media <span class="text-danger">*</span></label>
                            <select name="source_id" class="form-select form-select-sm" required>
                                <option value="">- Pilih -</option>
                                @foreach($sources as $source) <option value="{{ $source->id }}">{{ $source->name }}</option> @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="small fw-bold">Nama Pelapor (Opsional)</label>
                            <input type="text" name="reporter_name" class="form-control form-control-sm">
                        </div>
                    </div>

                    {{-- AREA CHECKLIST KELUHAN (DINAMIS DARI MASTER KATEGORI) --}}
                    <h6 class="fw-bold text-primary mb-3 border-bottom pb-2">Detail Keluhan (Centang yang sesuai)</h6>
                    <div class="row g-3">
                        @foreach($kategoriKeluhan as $kategori)
                        <div class="col-md-4">
                            <div class="card h-100 shadow-sm border-0">
                                <div class="card-header bg-white fw-bold text-primary py-2 small">{{ $kategori->name }}</div>
                                <div class="card-body small p-2">
                                    @foreach($kategori->items as $item)
                                    <div class="form-check mb-1">
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
                    <div class="row g-3 mt-1">
                        <div class="col-md-6">
                            <label class="small fw-bold">Lain-lain / Deskripsi Tambahan</label>
                            <textarea name="description" class="form-control form-control-sm" rows="3" placeholder="Tuliskan jika ada keluhan di luar pilihan di atas..."></textarea>
                        </div>
                        <div class="col-md-3">
                            <label class="small fw-bold">Unit Tujuan <span class="text-danger">*</span></label>
                            <select name="unit_destination" class="form-select form-select-sm" required>
                                <option value="">- Tujuan -</option>
                                @foreach($unitDestinations as $unit) <option value="{{ $unit->name }}">{{ $unit->name }}</option> @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="small fw-bold">Grade (Kegawatan) <span class="text-danger">*</span></label>
                            <select name="grade" class="form-select form-select-sm fw-bold" required>
                                <option value="">- Grade -</option>
                                @foreach($grades as $grade) <option value="{{ $grade->color_class ?? $grade->name }}">{{ $grade->name }}</option> @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-white border-top shadow-sm">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary fw-bold" id="btnSubmit">
                        <i class="bi bi-save me-1"></i> SIMPAN PENGADUAN
                    </button>
                    <button type="button" class="btn btn-primary fw-bold d-none" id="btnLoading" disabled>
                        <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Menyimpan...
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function showLoading() {
        document.getElementById('btnSubmit').classList.add('d-none');
        document.getElementById('btnLoading').classList.remove('d-none');
    }
</script>

@endsection