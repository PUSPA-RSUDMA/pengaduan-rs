@extends('layouts.admin')
@section('title', 'Koneksi WhatsApp')
@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Kolom Status & Barcode -->
        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3 fw-bold">
                    <i class="bi bi-whatsapp text-success me-2"></i> Status Perangkat WhatsApp
                </div>
                <div class="card-body text-center">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <div class="mb-3">
                        <span class="fw-bold">Status Koneksi: </span>
                        @if(isset($statusData['status']) && $statusData['status'] == 'WORKING')
                            <span class="badge bg-success fs-6">Terhubung (Connected)</span>
                        @else
                            <span class="badge bg-danger fs-6">Belum Terhubung (Disconnected)</span>
                        @endif
                    </div>

                    {{-- Jika belum terhubung, tampilkan Barcode --/--}}
                    @if(isset($statusData['status']) && $statusData['status'] != 'WORKING')
                        <div class="p-3 bg-light border rounded d-inline-block mb-3">
                            @if($qrCodeUrl)
                                <img src="{{ $qrCodeUrl }}" alt="QR Code WhatsApp" class="img-fluid" style="max-width: 250px;">
                            @else
                                <p class="text-muted m-0">Menyiapkan QR Code atau Gateway belum aktif...</p>
                            @endif
                        </div>
                        <p class="small text-muted">Buka aplikasi WhatsApp di HP Anda > Menu > Perangkat Tertaut > Tautkan Perangkat > Scan QR Code di atas.</p>
                        <br>
                        <a href="{{ route('whatsapp.index') }}" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-arrow-clockwise"></i> Refresh / Muat Ulang Barcode
                        </a>
                    @else
                        {{-- Jika sudah terhubung, tampilkan tombol putuskan/logout agar aman --}}
                        <div class="alert alert-success">
                            Perangkat Anda sudah tersambung dan siap mengirimkan pengingat logbook otomatis!
                        </div>
                        <form action="{{ route('whatsapp.disconnect') }}" method="POST" onsubmit="return confirm('Yakin ingin memutus perangkat WhatsApp ini?');">
                            @csrf
                            <button type="submit" class="btn btn-danger btn-sm fw-bold">
                                <i class="bi bi-box-arrow-right"></i> Putuskan Perangkat (Logout Aman)
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <!-- Kolom Uji Coba Kirim Pesan -->
        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3 fw-bold">
                    <i class="bi bi-send text-primary me-2"></i> Uji Coba Kirim Pesan WA
                </div>
                <div class="card-body">
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