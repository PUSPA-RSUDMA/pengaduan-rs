@extends('layouts.admin')
@section('title', 'Koneksi WhatsApp')
@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Kolom Scan QR Code / Dashboard Gateway via Iframe Online -->
        <div class="col-md-7 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <span class="fw-bold"><i class="bi bi-whatsapp text-success me-2"></i> Panel Koneksi WhatsApp (Gateway)</span>
                    <a href="http://rsudma.id:9093" target="_blank" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-box-arrow-up-right"></i> Buka Tab Baru
                    </a>
                </div>
                <div class="card-body p-2">
                    <p class="small text-muted px-2 m-1">Scan QR Code di bawah ini menggunakan aplikasi WhatsApp di HP Anda untuk menghubungkan perangkat.</p>
                    {{-- Menampilkan halaman gateway secara online di dalam aplikasi --}}
                    <div class="ratio ratio-4x3 border rounded overflow-hidden">
                        <iframe src="http://rsudma.id:9093" title="WhatsApp Gateway Dashboard" style="border:0;"></iframe>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kolom Uji Coba Kirim Pesan -->
        <div class="col-md-5 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3 fw-bold">
                    <i class="bi bi-send text-primary me-2"></i> Uji Coba Kirim Pesan WA
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('whatsapp.test') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nomor Tujuan WhatsApp</label>
                            <input type="text" name="nomor" class="form-control" value="085336102800" required>
                            <div class="form-text">Contoh: 085336102800</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Isi Pesan Uji Coba</label>
                            <textarea name="pesan" class="form-control" rows="4" required>Halo, ini adalah pesan uji coba dari sistem LaSehat.</textarea>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm fw-bold w-100">
                            <i class="bi bi-cursor-fill"></i> Kirim Pesan Tes Sekarang
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection