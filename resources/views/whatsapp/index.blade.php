@extends('layouts.admin')
@section('title', 'Koneksi WhatsApp')
@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Kolom Status & QR Code (Kiri) -->
        <div class="col-md-5 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3 fw-bold">
                    <i class="bi bi-qr-code-scan text-success me-2"></i> Status Perangkat WhatsApp
                </div>
                <div class="card-body text-center d-flex flex-column justify-content-center align-items-center py-5">
                    
                    <div id="wa-status-container">
                        <div class="spinner-border text-primary mb-3" role="status"></div>
                        <p class="text-muted">Memeriksa status mesin WhatsApp...</p>
                    </div>

                </div>
            </div>
        </div>

        <!-- Kolom Uji Coba Kirim Pesan (Kanan) -->
        <div class="col-md-7 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 fw-bold">
                    <i class="bi bi-send text-primary me-2"></i> Uji Coba Pesan WhatsApp
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

                    <div class="alert alert-info small">
                        <i class="bi bi-info-circle-fill me-1"></i> Sistem menggunakan <strong>Self-Hosted WhatsApp Gateway</strong> di Raspberry Pi Anda sendiri (Gratis & Aman).
                    </div>

                    <form action="{{ route('whatsapp.test') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nomor Tujuan WhatsApp</label>
                            <input type="text" name="nomor" class="form-control" value="085336102800" required>
                            <div class="form-text text-muted">Contoh: 085336102800</div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold">Isi Pesan Uji Coba</label>
                            <textarea name="pesan" class="form-control" rows="4" required>Halo, ini adalah pesan uji coba dari sistem RSUD via Raspberry Pi.</textarea>
                        </div>
                        <button type="submit" class="btn btn-primary fw-bold w-100 py-2">
                            <i class="bi bi-cursor-fill me-2"></i> Kirim Pesan Tes Sekarang
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const statusContainer = document.getElementById('wa-status-container');

    function fetchWAStatus() {
        fetch("{{ route('whatsapp.status') }}")
            .then(response => response.json())
            .then(data => {
                if (data.status === 'CONNECTED') {
                    statusContainer.innerHTML = `
                        <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
                        <h4 class="fw-bold mt-3 text-success">Terhubung</h4>
                        <p class="text-muted small">WhatsApp server aktif dan siap mengirim pesan otomatis.</p>
                    `;
                } else if (data.status === 'QR_READY') {
                    statusContainer.innerHTML = `
                        <h6 class="mb-3 fw-bold">Scan QR Code ini via WhatsApp HP:</h6>
                        <img src="${data.qr}" alt="QR Code WA" class="img-fluid border rounded shadow-sm bg-white p-2" style="max-width: 220px;">
                        <p class="text-muted mt-3 small">Buka WhatsApp > Menu > Perangkat Tertaut > Tautkan Perangkat</p>
                    `;
                } else if (data.status === 'DISCONNECTED') {
                    statusContainer.innerHTML = `
                        <i class="bi bi-x-circle-fill text-danger" style="font-size: 4rem;"></i>
                        <h4 class="fw-bold mt-3 text-danger">Terputus</h4>
                        <p class="text-muted small">Menunggu engine membuat sesi baru...</p>
                    `;
                } else {
                    statusContainer.innerHTML = `
                        <i class="bi bi-exclamation-triangle-fill text-warning" style="font-size: 4rem;"></i>
                        <h4 class="fw-bold mt-3 text-warning">Engine Mati</h4>
                        <p class="text-muted small">Pastikan script Node.js / PM2 berjalan di Raspberry Pi.</p>
                    `;
                }
            })
            .catch(error => console.error('Error fetching status:', error));
    }

    fetchWAStatus();
    setInterval(fetchWAStatus, 3000); // Refresh otomatis tiap 3 detik
});
</script>
@endsection