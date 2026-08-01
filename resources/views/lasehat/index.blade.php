@extends('layouts.admin')
@section('title', 'Data LaSehat')
@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center flex-wrap">
        <h5 class="fw-bold m-0"><i class="bi bi-list-ul me-2"></i>Daftar Pengantaran Pasien (LaSehat)</h5>
        <div class="mt-2 mt-md-0 d-flex gap-2">
            {{-- Tombol Sync Google Sheets --}}
            <a href="{{ route('lasehat.sync') }}" class="btn btn-success btn-sm fw-bold">
                <i class="bi bi-google"></i> Sync Spreadsheet
            </a>
            {{-- Tombol Tambah Manual --}}
            <button type="button" class="btn btn-primary btn-sm fw-bold" data-bs-toggle="modal" data-bs-target="#modalTambah">
                <i class="bi bi-plus-circle"></i> Tambah Manual
            </button>
        </div>
    </div>
    
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        <form method="GET" action="{{ route('lasehat.index') }}" class="row g-3 mb-4 align-items-end bg-light p-3 rounded shadow-sm">
            <div class="col-md-3">
                <label class="form-label small fw-bold">Dari Tanggal Pengantaran</label>
                <input type="date" name="start_date" class="form-control form-control-sm" value="{{ request('start_date') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-bold">Sampai Tanggal Pengantaran</label>
                <input type="date" name="end_date" class="form-control form-control-sm" value="{{ request('end_date') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-bold">Status Supir</label>
                <select name="status_supir" class="form-select form-select-sm">
                    <option value="">-- Semua Status Supir --</option>
                    <option value="sudah" {{ request('status_supir') == 'sudah' ? 'selected' : '' }}>Sudah Ada Supir</option>
                    <option value="belum" {{ request('status_supir') == 'belum' ? 'selected' : '' }}>Belum Ada Supir</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm fw-bold w-100">
                    <i class="bi bi-filter"></i> Filter
                </button>
                <a href="{{ route('lasehat.index') }}" class="btn btn-secondary btn-sm fw-bold" title="Reset Filter">
                    <i class="bi bi-arrow-counterclockwise"></i>
                </a>
            </div>
        </form>
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
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
                <tbody>
                    @foreach($lasehats as $item)
                    <tr>
                        <td class="fw-bold text-primary">{{ \Carbon\Carbon::parse($item->tanggal_pengantaran)->format('d/m/Y') }}</td>
                        <td>{{ $item->nama_pasien }}</td>
                        <td><span class="badge bg-secondary">{{ $item->tempat_dirawat }}</span></td>
                        <td>{{ Str::limit($item->alamat_tujuan, 30) }}</td>
                        <td>{{ $item->penanggung_jawab }}<br><small class="text-muted">{{ $item->no_telp_pj }}</small></td>
                        <td>
                            @if($item->created_by == 'Google Form')
                                <span class="badge bg-success"><i class="bi bi-google"></i> Spreadsheet</span>
                            @else
                                <span class="badge bg-info text-dark">Manual System</span>
                            @endif
                        </td>
                        <td>
                            @if($item->supir_ambulance)
                                <span class="fw-bold text-success"><i class="bi bi-check-circle"></i> {{ $item->supir_ambulance }}</span>
                                <button class="btn btn-sm btn-link py-0" data-bs-toggle="modal" data-bs-target="#modalSupir{{ $item->id }}">Ubah</button>
                            @else
                                <button class="btn btn-warning btn-sm fw-bold" data-bs-toggle="modal" data-bs-target="#modalSupir{{ $item->id }}">
                                    <i class="bi bi-person-plus"></i> Isi Supir
                                </button>
                            @endif
                        </td>
                        <td class="text-center">
                            <form action="{{ route('lasehat.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus data pasien {{ $item->nama_pasien }}?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" title="Hapus Data">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>

                    {{-- Modal Isi Supir (Sekarang pakai Select) --}}
                    {{-- Modal Isi Supir (Diperbaiki) --}}
                    <div class="modal fade" id="modalSupir{{ $item->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <form action="{{ route('lasehat.update_supir', $item->id) }}" method="POST">
                                    @csrf
                                    <div class="modal-header bg-warning text-dark">
                                        <h5 class="modal-title fw-bold">Pilih Supir Ambulance</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Nama Supir Ambulance</label>
                                            <select name="supir_ambulance" class="form-select" required>
                                                <option value="" disabled selected>-- Pilih Supir --</option>
                                                @foreach($supirs as $s)
                                                    <option value="{{ $s->nama_supir }}" {{ $item->supir_ambulance == $s->nama_supir ? 'selected' : '' }}>
                                                        {{ $s->nama_supir }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="modal-footer bg-light">
                                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-primary btn-sm">Simpan Supir</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{ $lasehats->links() }}
    </div>
</div>

{{-- Modal Tambah Manual --}}
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form action="{{ route('lasehat.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold">Tambah Data LaSehat (Manual)</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Nama Pasien</label>
                        <input type="text" name="nama_pasien" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Tempat Dirawat (Ruangan)</label>
                        <select name="tempat_dirawat" class="form-select" required>
                            <option value="">-- Pilih Ruangan --</option>
                            @foreach($ruangans as $r)
                                <option value="{{ $r }}">{{ $r }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Tanggal Pengantaran</label>
                        <input type="date" name="tanggal_pengantaran" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Supir Ambulance (Boleh Kosong)</label>
                        <select name="supir_ambulance" class="form-select">
                            <option value="">-- Nanti Saja / Belum Ada --</option>
                            @foreach($supirs as $s)
                                <option value="{{ $s->nama_supir }}">{{ $s->nama_supir }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Nama Penanggung Jawab</label>
                        <input type="text" name="penanggung_jawab" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">No. Telepon / WA PJ</label>
                        <input type="text" name="no_telp_pj" class="form-control" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Alamat Tujuan Pengantaran</label>
                        <textarea name="alamat_tujuan" class="form-control" rows="3" required></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Data</button>
            </div>
        </form>
    </div>
</div>
@endsection