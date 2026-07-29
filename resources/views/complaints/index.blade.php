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

                        <td>
                            @if(!empty($complaint->keluhan_sdm) && is_array($complaint->keluhan_sdm)) <div class="mb-1"><span class="badge bg-secondary">SDM:</span> {{ implode(', ', $complaint->keluhan_sdm) }}</div> @endif
                            @if(!empty($complaint->keluhan_sarpras) && is_array($complaint->keluhan_sarpras)) <div class="mb-1"><span class="badge bg-secondary">Sarpras:</span> {{ implode(', ', $complaint->keluhan_sarpras) }}</div> @endif
                            @if(!empty($complaint->keluhan_administrasi) && is_array($complaint->keluhan_administrasi)) <div class="mb-1"><span class="badge bg-secondary">Admin:</span> {{ implode(', ', $complaint->keluhan_administrasi) }}</div> @endif
                            @if(!empty($complaint->keluhan_farmasi) && is_array($complaint->keluhan_farmasi)) <div class="mb-1"><span class="badge bg-secondary">Farmasi:</span> {{ implode(', ', $complaint->keluhan_farmasi) }}</div> @endif
                            @if(!empty($complaint->keluhan_gizi) && is_array($complaint->keluhan_gizi)) <div class="mb-1"><span class="badge bg-secondary">Gizi:</span> {{ implode(', ', $complaint->keluhan_gizi) }}</div> @endif
                            @if(!empty($complaint->keluhan_keamanan) && is_array($complaint->keluhan_keamanan)) <div class="mb-1"><span class="badge bg-secondary">Aman:</span> {{ implode(', ', $complaint->keluhan_keamanan) }}</div> @endif
                            @if(!empty($complaint->description)) <div class="mb-1"><span class="badge bg-dark">Lainnya:</span> {{ $complaint->description }}</div> @endif
                        </td>

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

                                        <h6 class="fw-bold text-primary mb-3 border-bottom pb-2">Detail Keluhan</h6>
                                        <div class="row g-3">
                                            @php
                                                // Helper untuk mempermudah pengecekan data array checkbox
                                                $sdm = is_array($complaint->keluhan_sdm) ? $complaint->keluhan_sdm : [];
                                                $srp = is_array($complaint->keluhan_sarpras) ? $complaint->keluhan_sarpras : [];
                                                $adm = is_array($complaint->keluhan_administrasi) ? $complaint->keluhan_administrasi : [];
                                                $frm = is_array($complaint->keluhan_farmasi) ? $complaint->keluhan_farmasi : [];
                                                $gzi = is_array($complaint->keluhan_gizi) ? $complaint->keluhan_gizi : [];
                                                $kmn = is_array($complaint->keluhan_keamanan) ? $complaint->keluhan_keamanan : [];
                                            @endphp
                                            <!-- SDM -->
                                            <div class="col-md-4">
                                                <div class="card h-100 border-0">
                                                    <div class="card-header bg-white fw-bold text-primary py-2 small">1. SDM / Petugas</div>
                                                    <div class="card-body small p-2">
                                                        <div class="form-check"><input class="form-check-input" type="checkbox" name="keluhan_sdm[]" value="Etika & perilaku kurang ramah" {{ in_array("Etika & perilaku kurang ramah", $sdm) ? 'checked' : '' }}> Etika & perilaku kurang ramah</div>
                                                        <div class="form-check"><input class="form-check-input" type="checkbox" name="keluhan_sdm[]" value="Keterlambatan kehadiran" {{ in_array("Keterlambatan kehadiran", $sdm) ? 'checked' : '' }}> Keterlambatan kehadiran</div>
                                                        <div class="form-check"><input class="form-check-input" type="checkbox" name="keluhan_sdm[]" value="Komunikasi/penjelasan kurang" {{ in_array("Komunikasi/penjelasan kurang", $sdm) ? 'checked' : '' }}> Komunikasi/penjelasan kurang</div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Sarpras -->
                                            <div class="col-md-4">
                                                <div class="card h-100 border-0">
                                                    <div class="card-header bg-white fw-bold text-primary py-2 small">2. Sarpras</div>
                                                    <div class="card-body small p-2">
                                                        <div class="form-check"><input class="form-check-input" type="checkbox" name="keluhan_sarpras[]" value="Fasilitas rusak (AC, Toilet, dll)" {{ in_array("Fasilitas rusak (AC, Toilet, dll)", $srp) ? 'checked' : '' }}> Fasilitas rusak (AC, Toilet, dll)</div>
                                                        <div class="form-check"><input class="form-check-input" type="checkbox" name="keluhan_sarpras[]" value="Kebersihan kurang" {{ in_array("Kebersihan kurang", $srp) ? 'checked' : '' }}> Kebersihan kurang</div>
                                                        <div class="form-check"><input class="form-check-input" type="checkbox" name="keluhan_sarpras[]" value="Alat medis/umum tidak lengkap" {{ in_array("Alat medis/umum tidak lengkap", $srp) ? 'checked' : '' }}> Alat medis tidak lengkap</div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Administrasi -->
                                            <div class="col-md-4">
                                                <div class="card h-100 border-0">
                                                    <div class="card-header bg-white fw-bold text-primary py-2 small">3. Administrasi</div>
                                                    <div class="card-body small p-2">
                                                        <div class="form-check"><input class="form-check-input" type="checkbox" name="keluhan_administrasi[]" value="Antrean terlalu lama" {{ in_array("Antrean terlalu lama", $adm) ? 'checked' : '' }}> Antrean terlalu lama</div>
                                                        <div class="form-check"><input class="form-check-input" type="checkbox" name="keluhan_administrasi[]" value="Proses pendaftaran rumit" {{ in_array("Proses pendaftaran rumit", $adm) ? 'checked' : '' }}> Proses pendaftaran rumit</div>
                                                        <div class="form-check"><input class="form-check-input" type="checkbox" name="keluhan_administrasi[]" value="Masalah BPJS/Asuransi" {{ in_array("Masalah BPJS/Asuransi", $adm) ? 'checked' : '' }}> Masalah BPJS/Asuransi</div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Farmasi -->
                                            <div class="col-md-4">
                                                <div class="card h-100 border-0">
                                                    <div class="card-header bg-white fw-bold text-primary py-2 small">4. Farmasi</div>
                                                    <div class="card-body small p-2">
                                                        <div class="form-check"><input class="form-check-input" type="checkbox" name="keluhan_farmasi[]" value="Tunggu obat terlalu lama" {{ in_array("Tunggu obat terlalu lama", $frm) ? 'checked' : '' }}> Tunggu obat terlalu lama</div>
                                                        <div class="form-check"><input class="form-check-input" type="checkbox" name="keluhan_farmasi[]" value="Stok obat kosong" {{ in_array("Stok obat kosong", $frm) ? 'checked' : '' }}> Stok obat kosong</div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Gizi -->
                                            <div class="col-md-4">
                                                <div class="card h-100 border-0">
                                                    <div class="card-header bg-white fw-bold text-primary py-2 small">5. Gizi</div>
                                                    <div class="card-body small p-2">
                                                        <div class="form-check"><input class="form-check-input" type="checkbox" name="keluhan_gizi[]" value="Makanan terlambat" {{ in_array("Makanan terlambat", $gzi) ? 'checked' : '' }}> Makanan terlambat</div>
                                                        <div class="form-check"><input class="form-check-input" type="checkbox" name="keluhan_gizi[]" value="Rasa makanan hambar/dingin" {{ in_array("Rasa makanan hambar/dingin", $gzi) ? 'checked' : '' }}> Rasa hambar/dingin</div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Keamanan -->
                                            <div class="col-md-4">
                                                <div class="card h-100 border-0">
                                                    <div class="card-header bg-white fw-bold text-primary py-2 small">6. Keamanan & Parkir</div>
                                                    <div class="card-body small p-2">
                                                        <div class="form-check"><input class="form-check-input" type="checkbox" name="keluhan_keamanan[]" value="Parkir penuh/semrawut" {{ in_array("Parkir penuh/semrawut", $kmn) ? 'checked' : '' }}> Parkir penuh/semrawut</div>
                                                        <div class="form-check"><input class="form-check-input" type="checkbox" name="keluhan_keamanan[]" value="Barang hilang" {{ in_array("Barang hilang", $kmn) ? 'checked' : '' }}> Barang hilang</div>
                                                    </div>
                                                </div>
                                            </div>
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
                    <tr><td colspan="11" class="text-center py-4 text-muted">Data tidak ditemukan.</td></tr>
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

{{-- MODAL INPUT BARU (CHECKLIST 6 KATEGORI) --}}
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

                    {{-- AREA CHECKLIST KELUHAN --}}
                    <h6 class="fw-bold text-primary mb-3 border-bottom pb-2">Detail Keluhan (Centang yang sesuai)</h6>
                    <div class="row g-3">
                        
                        <!-- SDM -->
                        <div class="col-md-4">
                            <div class="card h-100 shadow-sm border-0">
                                <div class="card-header bg-white fw-bold text-primary py-2 small">1. SDM / Petugas</div>
                                <div class="card-body small p-2">
                                    <div class="form-check mb-1">
                                        <input class="form-check-input" type="checkbox" name="keluhan_sdm[]" value="Etika & perilaku kurang ramah" id="sdm1">
                                        <label class="form-check-label" for="sdm1">Etika & perilaku kurang ramah</label>
                                    </div>
                                    <div class="form-check mb-1">
                                        <input class="form-check-input" type="checkbox" name="keluhan_sdm[]" value="Keterlambatan kehadiran" id="sdm2">
                                        <label class="form-check-label" for="sdm2">Keterlambatan kehadiran</label>
                                    </div>
                                    <div class="form-check mb-1">
                                        <input class="form-check-input" type="checkbox" name="keluhan_sdm[]" value="Komunikasi/penjelasan kurang" id="sdm3">
                                        <label class="form-check-label" for="sdm3">Komunikasi/penjelasan kurang</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- SARPRAS -->
                        <div class="col-md-4">
                            <div class="card h-100 shadow-sm border-0">
                                <div class="card-header bg-white fw-bold text-primary py-2 small">2. Sarana & Prasarana</div>
                                <div class="card-body small p-2">
                                    <div class="form-check mb-1">
                                        <input class="form-check-input" type="checkbox" name="keluhan_sarpras[]" value="Fasilitas rusak (AC, Toilet, dll)" id="srp1">
                                        <label class="form-check-label" for="srp1">Fasilitas rusak (AC, Toilet, dll)</label>
                                    </div>
                                    <div class="form-check mb-1">
                                        <input class="form-check-input" type="checkbox" name="keluhan_sarpras[]" value="Kebersihan kurang" id="srp2">
                                        <label class="form-check-label" for="srp2">Kebersihan kurang</label>
                                    </div>
                                    <div class="form-check mb-1">
                                        <input class="form-check-input" type="checkbox" name="keluhan_sarpras[]" value="Alat medis/umum tidak lengkap" id="srp3">
                                        <label class="form-check-label" for="srp3">Alat medis/umum tidak lengkap</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ADMINISTRASI -->
                        <div class="col-md-4">
                            <div class="card h-100 shadow-sm border-0">
                                <div class="card-header bg-white fw-bold text-primary py-2 small">3. Administrasi & Antrean</div>
                                <div class="card-body small p-2">
                                    <div class="form-check mb-1">
                                        <input class="form-check-input" type="checkbox" name="keluhan_administrasi[]" value="Antrean terlalu lama" id="adm1">
                                        <label class="form-check-label" for="adm1">Antrean terlalu lama</label>
                                    </div>
                                    <div class="form-check mb-1">
                                        <input class="form-check-input" type="checkbox" name="keluhan_administrasi[]" value="Proses pendaftaran rumit" id="adm2">
                                        <label class="form-check-label" for="adm2">Proses pendaftaran rumit</label>
                                    </div>
                                    <div class="form-check mb-1">
                                        <input class="form-check-input" type="checkbox" name="keluhan_administrasi[]" value="Masalah BPJS/Asuransi" id="adm3">
                                        <label class="form-check-label" for="adm3">Masalah BPJS/Asuransi</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- FARMASI -->
                        <div class="col-md-4">
                            <div class="card h-100 shadow-sm border-0">
                                <div class="card-header bg-white fw-bold text-primary py-2 small">4. Farmasi / Obat</div>
                                <div class="card-body small p-2">
                                    <div class="form-check mb-1">
                                        <input class="form-check-input" type="checkbox" name="keluhan_farmasi[]" value="Tunggu obat terlalu lama" id="frm1">
                                        <label class="form-check-label" for="frm1">Tunggu obat terlalu lama</label>
                                    </div>
                                    <div class="form-check mb-1">
                                        <input class="form-check-input" type="checkbox" name="keluhan_farmasi[]" value="Stok obat kosong" id="frm2">
                                        <label class="form-check-label" for="frm2">Stok obat kosong</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- GIZI -->
                        <div class="col-md-4">
                            <div class="card h-100 shadow-sm border-0">
                                <div class="card-header bg-white fw-bold text-primary py-2 small">5. Gizi / Makanan</div>
                                <div class="card-body small p-2">
                                    <div class="form-check mb-1">
                                        <input class="form-check-input" type="checkbox" name="keluhan_gizi[]" value="Makanan terlambat" id="gz1">
                                        <label class="form-check-label" for="gz1">Makanan terlambat</label>
                                    </div>
                                    <div class="form-check mb-1">
                                        <input class="form-check-input" type="checkbox" name="keluhan_gizi[]" value="Rasa makanan hambar/dingin" id="gz2">
                                        <label class="form-check-label" for="gz2">Rasa makanan hambar/dingin</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- KEAMANAN -->
                        <div class="col-md-4">
                            <div class="card h-100 shadow-sm border-0">
                                <div class="card-header bg-white fw-bold text-primary py-2 small">6. Keamanan & Parkir</div>
                                <div class="card-body small p-2">
                                    <div class="form-check mb-1">
                                        <input class="form-check-input" type="checkbox" name="keluhan_keamanan[]" value="Parkir penuh/semrawut" id="km1">
                                        <label class="form-check-label" for="km1">Parkir penuh/semrawut</label>
                                    </div>
                                    <div class="form-check mb-1">
                                        <input class="form-check-input" type="checkbox" name="keluhan_keamanan[]" value="Barang hilang" id="km2">
                                        <label class="form-check-label" for="km2">Barang hilang</label>
                                    </div>
                                </div>
                            </div>
                        </div>
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