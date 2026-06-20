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

        {{-- BAGIAN FORM FILTER (PERBAIKAN: AUTO WIDTH) --}}
        <div class="bg-light p-3 rounded border">
            <form action="{{ route('complaints.index') }}" method="GET">
                
                {{-- Gunakan 'g-2' (Gap 2) supaya jaraknya pas, tidak terlalu jauh --}}
                <div class="row g-2 align-items-center">
                    
                    {{-- 1. FILTER TANGGAL --}}
                    {{-- Di Laptop dia ambil 3 kolom. Di HP Full. --}}
                    <div class="col-12 col-lg-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-white border-end-0"><i class="bi bi-calendar3"></i></span>
                            <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}" title="Mulai">
                            <span class="input-group-text bg-light border-start-0 border-end-0">s/d</span>
                            <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}" title="Sampai">
                        </div>
                    </div>

                    {{-- 2. UNIT DESTINATION --}}
                    {{-- Kita pakai 'col-lg-auto'. Lebarnya menyesuaikan panjang teks unit. --}}
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

                    {{-- 3. SEARCH --}}
                    {{-- Kita kasih col-lg-3 supaya kotak pencariannya agak lega buat ngetik --}}
                    <div class="col-6 col-lg-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                            <input type="text" name="search" class="form-control" placeholder="Cari..." value="{{ request('search') }}">
                        </div>
                    </div>

                    {{-- 4. SORT --}}
                    {{-- Pakai 'col-auto' biar nempel rapi sama kolom cari --}}
                    <div class="col-6 col-lg-auto">
                        <select name="sort" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="terbaru" {{ request('sort') == 'terbaru' ? 'selected' : '' }}>⬇️ Terbaru</option>
                            <option value="terlama" {{ request('sort') == 'terlama' ? 'selected' : '' }}>⬆️ Terlama</option>
                        </select>
                    </div>

                    {{-- 5. TOMBOL-TOMBOL (THE FIX) --}}
                    {{-- KUNCINYA DISINI: Pakai 'col' atau 'col-auto' tanpa angka. --}}
                    {{-- Ini akan memaksa tombol untuk masuk ke celah kosong di sebelah kanan --}}
                    <div class="col-auto d-flex gap-1">
                        
                        {{-- Tombol Filter Manual --}}
                        <button type="submit" class="btn btn-secondary btn-sm" title="Terapkan Filter">
                            <i class="bi bi-filter"></i>
                        </button>

                        {{-- Tombol Import --}}
                        <button type="button" class="btn btn-success btn-sm text-white" data-bs-toggle="modal" data-bs-target="#modalImport" title="Import Excel">
                            <i class="bi bi-file-earmark-spreadsheet"></i> <span class="d-none d-xl-inline">Import</span>
                        </button>
                        
                        {{-- Tombol PDF --}}
                        <a href="{{ route('export.pdf', request()->query()) }}" class="btn btn-danger btn-sm text-white" target="_blank" title="Download PDF">
                            <i class="bi bi-file-pdf"></i>
                        </a>

                        {{-- Tombol Excel --}}
                        <a href="{{ route('export.excel', request()->query()) }}" class="btn btn-success btn-sm" title="Download Excel">
                            <i class="bi bi-file-excel"></i>
                        </a>

                        {{-- Tombol Reset --}}
                        <a href="{{ route('complaints.index') }}" class="btn btn-outline-danger btn-sm" title="Reset Filter">
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
                        <th>Tgl Masuk</th>
                        <th>Unit Pelapor</th>
                        <th>Media</th>
                        <th>Grade</th>
                        <th width="25%">Isi Keluhan</th>
                        <th width="25%">Tindak Lanjut</th>
                        <th>Verifikasi</th>
                        <th>Unit Tujuan</th>
                        <th>Status</th>
                        <th>Aksi</th>
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
                                      style="background-color: {{ $complaint->grade }}; width: 15px; height: 15px; display: inline-block;" 
                                      title="{{ $complaint->grade }}"> </span>
                            @else
                                <span class="badge rounded-circle {{ $colorClass }} border border-white p-2" title="{{ $complaint->grade }}"> </span>
                            @endif
                        </td>

                        <td>{{ Str::limit($complaint->description) }}</td>

                        <td>{{ Str::limit($complaint->answer) }}</td>
                        <td class="text-center small text-muted">{{ $complaint->created_at->format('d/m/Y') }}</td>
                        <td class="fw-bold">{{ $complaint->unit_destination }}</td>
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

                    {{-- MODAL EDIT --}}
                    <div class="modal fade" id="modalEdit{{ $complaint->id }}" tabindex="-1" data-bs-backdrop="static">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content border-0 shadow-lg">
                                <div class="modal-header bg-warning text-dark py-2">
                                    <h6 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2"></i>Edit Pengaduan</h6>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <form action="{{ route('complaints.update', $complaint->id) }}" method="POST">
                                    @csrf @method('PUT')
                                    <div class="modal-body p-4">
                                        <div class="row g-3">
                                            <div class="col-md-6 border-end">
                                                <h6 class="fw-bold text-primary mb-3">Identitas & Waktu</h6>
                                                <div class="mb-2">
                                                    <label class="small fw-bold text-muted">Tanggal Masuk</label>
                                                    <input type="date" name="date" class="form-control form-control-sm" value="{{ \Carbon\Carbon::parse($complaint->date)->format('Y-m-d') }}" required>
                                                </div>
                                                <div class="mb-2">
                                                    <label class="small fw-bold text-muted">Unit Pelapor</label>
                                                    <select name="reporter_type" class="form-select form-select-sm" required>
                                                        @foreach($reporterTypes as $type)
                                                            <option value="{{ $type->name }}" {{ $complaint->reporter_type == $type->name ? 'selected' : '' }}>{{ $type->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="mb-2">
                                                    <label class="small fw-bold text-muted">Media Pengaduan</label>
                                                    <select name="source_id" class="form-select form-select-sm" required>
                                                        @foreach($sources as $source)
                                                            <option value="{{ $source->id }}" {{ $complaint->source_id == $source->id ? 'selected' : '' }}>{{ $source->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="mb-2">
                                                    <label class="small fw-bold text-muted">Nama Pelapor (Opsional)</label>
                                                    <input type="text" name="reporter_name" class="form-control form-control-sm" value="{{ $complaint->reporter_name }}">
                                                </div>
                                                <div class="mb-2">
                                                    <label class="small fw-bold text-muted">Tindak Lanjut</label>
                                                    <textarea name="answer" class="form-control form-control-sm" rows="3" required>{{ $complaint->answer }}</textarea>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <h6 class="fw-bold text-primary mb-3">Detail & Status</h6>
                                                <div class="mb-2">
                                                    <label class="small fw-bold text-muted">Unit Tujuan</label>
                                                    <select name="unit_destination" class="form-select form-select-sm" required>
                                                        @foreach($unitDestinations as $unit)
                                                            <option value="{{ $unit->name }}" {{ $complaint->unit_destination == $unit->name ? 'selected' : '' }}>{{ $unit->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="mb-2">
                                                    <label class="small fw-bold text-muted">Tingkat Kegawatan</label>
                                                    <select name="grade" class="form-select form-select-sm fw-bold">
                                                        @foreach($grades as $grade)
                                                            <option value="{{ $grade->color_class ?? $grade->name }}" {{ $complaint->grade == ($grade->color_class ?? $grade->name) ? 'selected' : '' }}>
                                                                {{ $grade->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="mb-2">
                                                    <label class="small fw-bold text-muted">Status Penyelesaian</label>
                                                    <select name="status" class="form-select form-select-sm fw-bold border-warning">
                                                        <option value="Pending" {{ $complaint->status == 'Pending' ? 'selected' : '' }}>⏳ Pending</option>
                                                        <option value="Proses" {{ $complaint->status == 'Proses' ? 'selected' : '' }}>🔄 Proses</option>
                                                        <option value="Selesai" {{ $complaint->status == 'Selesai' ? 'selected' : '' }}>✅ Selesai</option>
                                                    </select>
                                                </div>
                                                <div class="mb-2">
                                                    <label class="small fw-bold text-muted">Isi Keluhan</label>
                                                    <textarea name="description" class="form-control form-control-sm" rows="3" required>{{ $complaint->description }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer bg-light py-1">
                                        <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-warning btn-sm fw-bold">Update Data</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    {{-- END MODAL EDIT --}}

                    @empty
                    <tr><td colspan="10" class="text-center py-4">Data tidak ditemukan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3 d-flex justify-content-end">
            {{ $complaints->links() }}
        </div>
    </div>
</div>

{{-- MODAL IMPORT EXCEL (VERSI DOWNLOAD LANGSUNG KE PUBLIC) --}}
<div class="modal" id="modalImport" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title fw-bold"><i class="bi bi-file-earmark-excel me-2"></i>Import Data Excel</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <form action="{{ route('complaints.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    
                    {{-- OPSI 1: DOWNLOAD TEMPLATE --}}
                    <div class="mb-4 text-center border-bottom pb-4">
                        <p class="fw-bold mb-2">Belum punya format Excel-nya?</p>
                        
                        {{-- TOMBOL DOWNLOAD LANGSUNG KE FILE PUBLIC --}}
                        <a href="{{ asset('template_pengaduan.xlsx') }}" class="btn btn-outline-success w-100 fw-bold border-2 shadow-sm" download>
                            <i class="bi bi-download me-2"></i> Download Template Excel
                        </a>
                        
                        <small class="text-muted d-block mt-2 fst-italic">
                            *Silakan download, isi data, lalu upload kembali file tersebut.
                        </small>
                    </div>

                    {{-- OPSI 2: UPLOAD FILE --}}
                    <div class="mb-2">
                        <label class="form-label fw-bold">Upload File Excel (.xlsx / .xls)</label>
                        <input type="file" name="file" class="form-control" required>
                    </div>

                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success fw-bold">
                        <i class="bi bi-cloud-upload-fill me-1"></i> Upload & Import
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL INPUT BARU (TIDAK BERUBAH) --}}
<div class="modal fade" id="modalInput" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg">
            
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-table me-2"></i>Input Laporan Baru
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            
            <form action="{{ route('complaints.store') }}" method="POST" id="formSimpan" onsubmit="showLoading()" class="d-flex flex-column" style="overflow: hidden;">
                @csrf
                
                <div class="modal-body p-3 bg-light" style="overflow-y: auto;">
                    
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <small class="text-muted fst-italic">* Data pilihan di bawah ini diambil langsung dari Data Master.</small>
                        <button type="button" class="btn btn-success btn-sm shadow-sm" onclick="addRow()">
                            <i class="bi bi-plus-circle-fill me-1"></i> Tambah Baris
                        </button>
                    </div>

                    <div class="table-responsive bg-white shadow-sm border rounded">
                        <table class="table table-bordered table-sm align-middle mb-0" id="tableInput">
                            <thead class="table-dark text-center small sticky-top">
                                <tr>
                                    <th style="width: 12%">Tanggal</th>
                                    <th style="width: 15%">Unit Pelapor</th>
                                    <th style="width: 15%">Media & Nama</th>
                                    <th>Isi Keluhan</th>
                                    <th style="width: 15%">Unit Tujuan</th>
                                    <th style="width: 12%">Kegawatan</th>
                                    <th style="width: 5%"><i class="bi bi-trash"></i></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <input type="date" name="inputs[0][date]" class="form-control form-control-sm mb-1" value="{{ date('Y-m-d') }}" required>
                                    </td>
                                    <td>
                                        <select name="inputs[0][reporter_type]" class="form-select form-select-sm" required>
                                            <option value="">- Pilih -</option>
                                            @foreach($reporterTypes as $type)
                                                <option value="{{ $type->name }}">{{ $type->name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <select name="inputs[0][source_id]" class="form-select form-select-sm mb-1" required>
                                            <option value="">- Media -</option>
                                            @foreach($sources as $source)
                                                <option value="{{ $source->id }}">{{ $source->name }}</option>
                                            @endforeach
                                        </select>
                                        <input type="text" name="inputs[0][reporter_name]" class="form-control form-control-sm" placeholder="Nama (Opsional)">
                                    </td>
                                    <td>
                                        <textarea name="inputs[0][description]" class="form-control form-control-sm" rows="2" placeholder="Keluhan..." required></textarea>
                                    </td>
                                    <td>
                                        <select name="inputs[0][unit_destination]" class="form-select form-select-sm" required>
                                            <option value="">- Tujuan -</option>
                                            @foreach($unitDestinations as $unit)
                                                <option value="{{ $unit->name }}">{{ $unit->name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <select name="inputs[0][grade]" class="form-select form-select-sm fw-bold">
                                            @foreach($grades as $grade)
                                                <option value="{{ $grade->color_class ?? $grade->name }}">
                                                    {{ $grade->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-danger btn-sm disabled" disabled>
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="modal-footer bg-white border-top shadow-sm">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary fw-bold" id="btnSubmit">
                        <i class="bi bi-save me-1"></i> SIMPAN SEMUA
                    </button>
                    <button type="button" class="btn btn-primary fw-bold d-none" id="btnLoading" disabled>
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
    const masterReporterTypes = {!! json_encode($reporterTypes) !!};
    const masterSources = {!! json_encode($sources) !!};
    const masterUnits = {!! json_encode($unitDestinations) !!};
    const masterGrades = {!! json_encode($grades) !!};

    function addRow() {
        ++i;
        let table = document.getElementById('tableInput').getElementsByTagName('tbody')[0];
        let newRow = table.insertRow(table.rows.length);
        
        let optionsReporter = '<option value="">- Pilih -</option>';
        masterReporterTypes.forEach(r => optionsReporter += `<option value="${r.name}">${r.name}</option>`);

        let optionsSource = '<option value="">- Media -</option>';
        masterSources.forEach(s => optionsSource += `<option value="${s.id}">${s.name}</option>`);

        let optionsUnit = '<option value="">- Tujuan -</option>';
        masterUnits.forEach(u => optionsUnit += `<option value="${u.name}">${u.name}</option>`);

        let optionsGrade = '';
        masterGrades.forEach(g => {
            let val = g.color_class ? g.color_class : g.name;
            optionsGrade += `<option value="${val}">${g.name}</option>`;
        });

        let html = `
            <td><input type="date" name="inputs[${i}][date]" class="form-control form-control-sm" value="{{ date('Y-m-d') }}" required></td>
            <td><select name="inputs[${i}][reporter_type]" class="form-select form-select-sm" required>${optionsReporter}</select></td>
            <td>
                <select name="inputs[${i}][source_id]" class="form-select form-select-sm mb-1" required>${optionsSource}</select>
                <input type="text" name="inputs[${i}][reporter_name]" class="form-control form-control-sm" placeholder="Nama">
            </td>
            <td><textarea name="inputs[${i}][description]" class="form-control form-control-sm" rows="2" placeholder="Keluhan..." required></textarea></td>
            <td><select name="inputs[${i}][unit_destination]" class="form-select form-select-sm" required>${optionsUnit}</select></td>
            <td><select name="inputs[${i}][grade]" class="form-select form-select-sm fw-bold">${optionsGrade}</select></td>
            <td class="text-center"><button type="button" class="btn btn-outline-danger btn-sm" onclick="removeRow(this)"><i class="bi bi-x-lg"></i></button></td>
        `;
        newRow.innerHTML = html;
    }

    function removeRow(btn) {
        let row = btn.parentNode.parentNode;
        row.parentNode.removeChild(row);
    }

    function showLoading() {
        document.getElementById('btnSubmit').classList.add('d-none');
        document.getElementById('btnLoading').classList.remove('d-none');
    }
</script>

@endsection