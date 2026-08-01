@extends('layouts.admin')
@section('title', 'Data LaSehat')
@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold m-0"><i class="bi bi-list-ul me-2"></i>Daftar Pengantaran Pasien (LaSehat)</h5>
    </div>
    
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Tgl Order</th>
                        <th>Tgl Pengantaran</th>
                        <th>Nama Pasien</th>
                        <th>Ruangan</th>
                        <th>Alamat Tujuan</th>
                        <th>No Telp PJ</th>
                        <th>Sumber</th>
                        <th>Supir Ambulance</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($lasehats as $item)
                    <tr>
                        <td>{{ $item->created_at->format('d/m/Y H:i') }}</td>
                        <td class="fw-bold text-primary">{{ \Carbon\Carbon::parse($item->tanggal_pengantaran)->format('d/m/Y') }}</td>
                        <td>{{ $item->nama_pasien }}</td>
                        <td><span class="badge bg-secondary">{{ $item->tempat_dirawat }}</span></td>
                        <td>{{ Str::limit($item->alamat_tujuan, 30) }}</td>
                        <td>{{ $item->no_telp_pj }}<br><small class="text-muted">{{ $item->penanggung_jawab }}</small></td>
                        <td>
                            @if($item->created_by == 'Google Form')
                                <span class="badge bg-success"><i class="bi bi-google"></i> Google Form</span>
                            @else
                                <span class="badge bg-info text-dark">Manual / System</span>
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
                    </tr>

                    {{-- Modal Isi Supir --}}
                    <div class="modal fade" id="modalSupir{{ $item->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <form action="{{ route('lasehat.update_supir', $item->id) }}" method="POST" class="modal-content">
                                @csrf
                                <div class="modal-header bg-warning">
                                    <h5 class="modal-title fw-bold">Penugasan Supir Ambulance</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <label class="form-label">Nama Supir Ambulance</label>
                                    <input type="text" name="supir_ambulance" class="form-control" value="{{ $item->supir_ambulance }}" placeholder="Masukkan nama supir..." required>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-primary">Simpan Supir</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{ $lasehats->links() }}
    </div>
</div>
@endsection