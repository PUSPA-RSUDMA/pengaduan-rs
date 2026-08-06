@extends('layouts.admin')

@section('title', 'Cari Peserta BPJS')

@section('content')
<div class="row">
    {{-- BAGIAN KIRI: Form Pencarian --}}
    <div class="col-md-4 mb-4">
        <div class="card border-0 shadow-sm rounded-4 position-sticky" style="top: 80px;">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3"><i class="bi bi-search me-2 text-primary"></i>Cari No. Kartu / NIK</h6>
                
                <form id="formCariPeserta">
                    <div class="mb-3">
                        <label for="no_kartu" class="form-label text-muted small fw-semibold">Nomor Kartu / NIK</label>
                        <input type="text" class="form-control bg-light" id="no_kartu" placeholder="Masukkan 13 digit No Kartu / 16 digit NIK..." required autocomplete="off" style="border-radius: 10px;">
                    </div>
                    <button type="submit" class="btn btn-primary w-100 fw-medium shadow-sm" id="btnCari" style="border-radius: 10px;">
                        <span id="btnText">Cari Data Peserta</span>
                        <span id="btnLoading" class="spinner-border spinner-border-sm ms-2 d-none" role="status" aria-hidden="true"></span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- BAGIAN KANAN: Hasil Pencarian --}}
    <div class="col-md-8">
        {{-- Alert Error --}}
        <div class="alert alert-danger d-none rounded-4 shadow-sm mb-4 border-0" id="errorCard" role="alert">
            <div class="d-flex align-items-center">
                <i class="bi bi-exclamation-triangle-fill fs-4 me-3"></i>
                <div id="errorMessage" style="font-size: 0.9rem;"></div>
            </div>
        </div>

        {{-- Kartu Hasil --}}
        <div class="card border-0 shadow-sm rounded-4 d-none" id="resultCard">
            <div class="card-body p-4 p-md-5">
                
                {{-- HEADER NAMA & STATUS --}}
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 pb-3 border-bottom">
                    <div>
                        <h4 class="fw-bold text-dark mb-1" id="resNama">-</h4>
                        <span class="text-muted small" id="resSexTglLahir">-</span>
                    </div>
                    <div class="mt-2 mt-md-0">
                        <span class="badge px-3 py-2" id="resStatus" style="font-size: 0.85rem; border-radius: 8px;">-</span>
                    </div>
                </div>

                <div class="row g-4">
                    {{-- BLOK 1: IDENTITAS DIRI --}}
                    <div class="col-md-6">
                        <h6 class="fw-bold text-primary mb-3"><i class="bi bi-person-vcard me-2"></i>Identitas Diri</h6>
                        <table class="table table-borderless table-sm small">
                            <tbody>
                                <tr>
                                    <td class="text-muted w-50">No. Kartu BPJS</td>
                                    <td class="fw-semibold text-dark" id="resNoKartu">-</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">NIK KTP</td>
                                    <td class="fw-semibold text-dark" id="resNik">-</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">No. Rekam Medis</td>
                                    <td class="fw-semibold text-dark" id="resNoMr">-</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Nomor Telepon</td>
                                    <td class="fw-semibold text-dark" id="resNoTelp">-</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Usia Saat Ini</td>
                                    <td class="fw-semibold text-dark" id="resUmur">-</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    {{-- BLOK 2: DATA KEPESERTAAN --}}
                    <div class="col-md-6">
                        <h6 class="fw-bold text-primary mb-3"><i class="bi bi-hospital me-2"></i>Data Kepesertaan</h6>
                        <table class="table table-borderless table-sm small">
                            <tbody>
                                <tr>
                                    <td class="text-muted w-50">Jenis Peserta</td>
                                    <td class="fw-semibold text-dark" id="resJenisPeserta">-</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Hak Kelas</td>
                                    <td class="fw-semibold text-dark" id="resKelas">-</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Faskes Tingkat 1</td>
                                    <td class="fw-semibold text-dark" id="resFaskes">-</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">TMT (Aktif Sejak)</td>
                                    <td class="fw-semibold text-dark" id="resTmt">-</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Asuransi Lain (COB)</td>
                                    <td class="fw-semibold text-dark" id="resCob">-</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- BLOK 3: INFORMASI TAMBAHAN --}}
                <div class="mt-4 pt-3 border-top">
                    <h6 class="fw-bold text-primary mb-3"><i class="bi bi-info-square me-2"></i>Informasi Tambahan</h6>
                    <div class="bg-light p-3 rounded-3 small">
                        <div class="row">
                            <div class="col-md-6 mb-2 mb-md-0">
                                <span class="text-muted d-block mb-1">Status Dinsos / SKTM</span>
                                <span class="fw-semibold" id="resDinsos">-</span>
                            </div>
                            <div class="col-md-6">
                                <span class="text-muted d-block mb-1">Program PRB / Prolanis</span>
                                <span class="fw-semibold text-danger" id="resProlanis">-</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('formCariPeserta').addEventListener('submit', function(e) {
        e.preventDefault();
        
        let noKartu = document.getElementById('no_kartu').value;
        let btnText = document.getElementById('btnText');
        let btnLoading = document.getElementById('btnLoading');
        let btnCari = document.getElementById('btnCari');
        
        let resultCard = document.getElementById('resultCard');
        let errorCard = document.getElementById('errorCard');

        // State Loading
        btnText.innerText = 'Mencari...';
        btnLoading.classList.remove('d-none');
        btnCari.setAttribute('disabled', 'true');
        resultCard.classList.add('d-none');
        errorCard.classList.add('d-none');

        // Panggil endpoint API Lokal kita
        fetch(`{{ route('bpjs.cari') }}?no_kartu=${noKartu}`)
            .then(response => {
                // Tangkap jika terjadi error 500 dari server sebelum di-parse ke JSON
                if (!response.ok) { throw new Error(`HTTP error! status: ${response.status}`); }
                return response.json();
            })
            .then(data => {
                // Kembalikan tombol
                btnText.innerText = 'Cari Data Peserta';
                btnLoading.classList.add('d-none');
                btnCari.removeAttribute('disabled');

                // Sukses
                if (data.metaData && data.metaData.code == "200") {
                    let p = data.response.peserta;

                    // Header Info
                    document.getElementById('resNama').innerText = p.nama;
                    let gender = (p.sex === 'L') ? 'Laki-laki' : 'Perempuan';
                    document.getElementById('resSexTglLahir').innerText = `${gender} • Lahir: ${p.tglLahir}`;
                    
                    // Identitas
                    document.getElementById('resNoKartu').innerText = p.noKartu;
                    document.getElementById('resNik').innerText = p.nik;
                    document.getElementById('resNoMr').innerText = (p.mr && p.mr.noMR) ? p.mr.noMR : '-';
                    document.getElementById('resNoTelp').innerText = (p.mr && p.mr.noTelepon) ? p.mr.noTelepon : '-';
                    document.getElementById('resUmur').innerText = (p.umur && p.umur.umurSekarang) ? p.umur.umurSekarang : '-';

                    // Kepesertaan
                    document.getElementById('resJenisPeserta').innerText = (p.jenisPeserta && p.jenisPeserta.keterangan) ? p.jenisPeserta.keterangan : '-';
                    document.getElementById('resKelas').innerText = (p.hakKelas && p.hakKelas.keterangan) ? p.hakKelas.keterangan : '-';
                    document.getElementById('resFaskes').innerText = (p.provUmum && p.provUmum.nmProvider) ? `${p.provUmum.kdProvider} - ${p.provUmum.nmProvider}` : '-';
                    document.getElementById('resTmt').innerText = p.tglTMT;
                    
                    // Asuransi Tambahan
                    document.getElementById('resCob').innerText = (p.cob && p.cob.nmAsuransi) ? p.cob.nmAsuransi : 'Tidak Ada';

                    // Info Tambahan (Dinsos / Prolanis)
                    let infoSKTM = (p.informasi && p.informasi.noSKTM) ? p.informasi.noSKTM : 'Tidak Ada';
                    document.getElementById('resDinsos').innerText = infoSKTM;
                    
                    let infoProlanis = (p.informasi && p.informasi.prolanisPRB) ? p.informasi.prolanisPRB : 'Tidak Ada Riwayat';
                    document.getElementById('resProlanis').innerText = infoProlanis;

                    // Mengatur warna badge Status
                    let statusBadge = document.getElementById('resStatus');
                    let statusNama = (p.statusPeserta && p.statusPeserta.keterangan) ? p.statusPeserta.keterangan : 'TIDAK DIKETAHUI';
                    let statusKode = (p.statusPeserta && p.statusPeserta.kode) ? p.statusPeserta.kode : 'X';
                    
                    statusBadge.innerText = statusNama;
                    
                    // BPJS: 0 biasanya Aktif, sisanya Non Aktif (meninggal, tunggakan, dll)
                    if (statusKode === "0") { 
                        statusBadge.className = "badge bg-success bg-opacity-10 text-success border border-success";
                    } else {
                        statusBadge.className = "badge bg-danger bg-opacity-10 text-danger border border-danger";
                    }

                    resultCard.classList.remove('d-none');
                } else {
                    // Tampilkan Error dari BPJS (Misal: Kartu tidak ditemukan)
                    let msg = (data.metaData && data.metaData.message) ? data.metaData.message : 'Terjadi kesalahan tidak dikenal.';
                    document.getElementById('errorMessage').innerHTML = `<strong>Pencarian Gagal!</strong><br>${msg}`;
                    errorCard.classList.remove('d-none');
                }
            })
            .catch(error => {
                btnText.innerText = 'Cari Data Peserta';
                btnLoading.classList.add('d-none');
                btnCari.removeAttribute('disabled');
                
                document.getElementById('errorMessage').innerHTML = `<strong>Server Error!</strong><br>Terjadi gangguan koneksi atau internal server. <br><small class="text-dark">${error.message}</small>`;
                errorCard.classList.remove('d-none');
                console.error("Fetch Error:", error);
            });
    });
</script>
@endsection