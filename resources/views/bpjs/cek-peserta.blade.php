@extends('layouts.admin')

@section('title', 'Cari Peserta & Rujukan BPJS')

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
                                <tr><td class="text-muted w-50">No. Kartu BPJS</td><td class="fw-semibold text-dark" id="resNoKartu">-</td></tr>
                                <tr><td class="text-muted">NIK KTP</td><td class="fw-semibold text-dark" id="resNik">-</td></tr>
                                <tr><td class="text-muted">No. Rekam Medis</td><td class="fw-semibold text-dark" id="resNoMr">-</td></tr>
                                <tr><td class="text-muted">Nomor Telepon</td><td class="fw-semibold text-dark" id="resNoTelp">-</td></tr>
                                <tr><td class="text-muted">Usia Saat Ini</td><td class="fw-semibold text-dark" id="resUmur">-</td></tr>
                            </tbody>
                        </table>
                    </div>

                    {{-- BLOK 2: DATA KEPESERTAAN --}}
                    <div class="col-md-6">
                        <h6 class="fw-bold text-primary mb-3"><i class="bi bi-hospital me-2"></i>Data Kepesertaan</h6>
                        <table class="table table-borderless table-sm small">
                            <tbody>
                                <tr><td class="text-muted w-50">Jenis Peserta</td><td class="fw-semibold text-dark" id="resJenisPeserta">-</td></tr>
                                <tr><td class="text-muted">Hak Kelas</td><td class="fw-semibold text-dark" id="resKelas">-</td></tr>
                                <tr><td class="text-muted">Faskes Tingkat 1</td><td class="fw-semibold text-dark" id="resFaskes">-</td></tr>
                                <tr><td class="text-muted">TMT (Aktif Sejak)</td><td class="fw-semibold text-dark" id="resTmt">-</td></tr>
                                <tr><td class="text-muted">Asuransi Lain (COB)</td><td class="fw-semibold text-dark" id="resCob">-</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- BLOK 3: INFORMASI RUJUKAN AKTIF (TAMBAHAN BARU) --}}
                <div class="mt-4 pt-4 border-top">
                    <h6 class="fw-bold text-primary mb-3"><i class="bi bi-file-medical-fill me-2"></i>Informasi Rujukan Aktif</h6>
                    
                    <div class="row g-3">
                        {{-- Rujukan PCare --}}
                        <div class="col-md-6">
                            <div class="card bg-light border-0 h-100 rounded-3">
                                <div class="card-body p-3 small">
                                    <h6 class="fw-bold text-dark border-bottom pb-2 mb-2">Dari Faskes 1 (Puskesmas/Klinik)</h6>
                                    <div id="resRujukanPcare"></div>
                                </div>
                            </div>
                        </div>

                        {{-- Rujukan RS --}}
                        <div class="col-md-6">
                            <div class="card bg-light border-0 h-100 rounded-3">
                                <div class="card-body p-3 small">
                                    <h6 class="fw-bold text-dark border-bottom pb-2 mb-2">Dari Faskes 2 (Rumah Sakit)</h6>
                                    <div id="resRujukanRS"></div>
                                </div>
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
        btnText.innerText = 'Memeriksa BPJS...';
        btnLoading.classList.remove('d-none');
        btnCari.setAttribute('disabled', 'true');
        resultCard.classList.add('d-none');
        errorCard.classList.add('d-none');

        // Panggil endpoint
        fetch(`{{ route('bpjs.cari') }}?no_kartu=${noKartu}`)
            .then(response => {
                if (!response.ok) { throw new Error(`HTTP error! status: ${response.status}`); }
                return response.json();
            })
            .then(data => {
                btnText.innerText = 'Cari Data Peserta';
                btnLoading.classList.add('d-none');
                btnCari.removeAttribute('disabled');

                if (data.metaData && data.metaData.code == "200") {
                    let p = data.response.peserta;

                    // Isi Identitas & Kepesertaan
                    document.getElementById('resNama').innerText = p.nama;
                    document.getElementById('resSexTglLahir').innerText = `${(p.sex === 'L') ? 'Laki-laki' : 'Perempuan'} • Lahir: ${p.tglLahir}`;
                    document.getElementById('resNoKartu').innerText = p.noKartu;
                    document.getElementById('resNik').innerText = p.nik;
                    document.getElementById('resNoMr').innerText = (p.mr && p.mr.noMR) ? p.mr.noMR : '-';
                    document.getElementById('resNoTelp').innerText = (p.mr && p.mr.noTelepon) ? p.mr.noTelepon : '-';
                    document.getElementById('resUmur').innerText = (p.umur && p.umur.umurSekarang) ? p.umur.umurSekarang : '-';
                    document.getElementById('resJenisPeserta').innerText = (p.jenisPeserta && p.jenisPeserta.keterangan) ? p.jenisPeserta.keterangan : '-';
                    document.getElementById('resKelas').innerText = (p.hakKelas && p.hakKelas.keterangan) ? p.hakKelas.keterangan : '-';
                    document.getElementById('resFaskes').innerText = (p.provUmum && p.provUmum.nmProvider) ? `${p.provUmum.kdProvider} - ${p.provUmum.nmProvider}` : '-';
                    document.getElementById('resTmt').innerText = p.tglTMT;
                    document.getElementById('resCob').innerText = (p.cob && p.cob.nmAsuransi) ? p.cob.nmAsuransi : 'Tidak Ada';

                    // Atur Status Peserta
                    let statusBadge = document.getElementById('resStatus');
                    let statusKode = (p.statusPeserta && p.statusPeserta.kode) ? p.statusPeserta.kode : 'X';
                    statusBadge.innerText = (p.statusPeserta && p.statusPeserta.keterangan) ? p.statusPeserta.keterangan : 'TIDAK DIKETAHUI';
                    statusBadge.className = (statusKode === "0") 
                        ? "badge bg-success bg-opacity-10 text-success border border-success" 
                        : "badge bg-danger bg-opacity-10 text-danger border border-danger";

                    // ==========================================
                    // FITUR BARU: RENDER DATA RUJUKAN PCARE
                    // ==========================================
                    let boxPcare = document.getElementById('resRujukanPcare');
                    if (data.response.rujukan_pcare) {
                        let r = data.response.rujukan_pcare;
                        boxPcare.innerHTML = `
                            <div class="mb-1"><span class="text-muted d-inline-block w-25">No. Kunj</span> <span class="fw-bold text-dark">: ${r.noKunjungan}</span></div>
                            <div class="mb-1"><span class="text-muted d-inline-block w-25">Tgl</span> <span class="fw-semibold text-dark">: ${r.tglKunjungan}</span></div>
                            <div class="mb-1"><span class="text-muted d-inline-block w-25">Poli</span> <span class="fw-semibold text-danger">: ${r.poliRujukan?.nama || '-'}</span></div>
                            <div class="mb-1"><span class="text-muted d-inline-block w-25">Perujuk</span> <span class="fw-semibold text-dark">: ${r.provPerujuk?.nama || '-'}</span></div>
                            <div class="mb-0"><span class="text-muted d-inline-block w-25">Diagnosa</span> <span class="fw-semibold text-dark">: ${r.diagnosa?.nama || '-'}</span></div>
                        `;
                    } else {
                        boxPcare.innerHTML = `<div class="text-center py-2 text-muted fst-italic"><i class="bi bi-x-circle d-block mb-1 fs-5"></i>Tidak ada rujukan aktif</div>`;
                    }

                    // ==========================================
                    // FITUR BARU: RENDER DATA RUJUKAN RS
                    // ==========================================
                    let boxRS = document.getElementById('resRujukanRS');
                    if (data.response.rujukan_rs) {
                        let r = data.response.rujukan_rs;
                        boxRS.innerHTML = `
                            <div class="mb-1"><span class="text-muted d-inline-block w-25">No. Kunj</span> <span class="fw-bold text-dark">: ${r.noKunjungan}</span></div>
                            <div class="mb-1"><span class="text-muted d-inline-block w-25">Tgl</span> <span class="fw-semibold text-dark">: ${r.tglKunjungan}</span></div>
                            <div class="mb-1"><span class="text-muted d-inline-block w-25">Poli</span> <span class="fw-semibold text-danger">: ${r.poliRujukan?.nama || '-'}</span></div>
                            <div class="mb-1"><span class="text-muted d-inline-block w-25">Perujuk</span> <span class="fw-semibold text-dark">: ${r.provPerujuk?.nama || '-'}</span></div>
                            <div class="mb-0"><span class="text-muted d-inline-block w-25">Diagnosa</span> <span class="fw-semibold text-dark">: ${r.diagnosa?.nama || '-'}</span></div>
                        `;
                    } else {
                        boxRS.innerHTML = `<div class="text-center py-2 text-muted fst-italic"><i class="bi bi-x-circle d-block mb-1 fs-5"></i>Tidak ada rujukan aktif</div>`;
                    }

                    resultCard.classList.remove('d-none');
                } else {
                    let msg = (data.metaData && data.metaData.message) ? data.metaData.message : 'Terjadi kesalahan.';
                    document.getElementById('errorMessage').innerHTML = `<strong>Pencarian Gagal!</strong><br>${msg}`;
                    errorCard.classList.remove('d-none');
                }
            })
            .catch(error => {
                btnText.innerText = 'Cari Data Peserta';
                btnLoading.classList.add('d-none');
                btnCari.removeAttribute('disabled');
                
                document.getElementById('errorMessage').innerHTML = `<strong>Server Error!</strong><br>Terjadi gangguan koneksi. <br><small class="text-dark">${error.message}</small>`;
                errorCard.classList.remove('d-none');
                console.error("Fetch Error:", error);
            });
    });
</script>
@endsection