@extends('layouts.admin')

@section('title', 'Cari Peserta BPJS')

@section('content')
<div class="row">
    <div class="col-md-5 mb-4">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3"><i class="bi bi-search me-2 text-primary"></i>Pencarian Berdasarkan No. Kartu</h6>
                
                <form id="formCariPeserta">
                    <div class="mb-3">
                        <label for="no_kartu" class="form-label text-muted small fw-semibold">Nomor Kartu BPJS</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="no_kartu" placeholder="Masukkan 13 Digit Nomor Kartu..." required autocomplete="off">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 fw-medium" id="btnCari">
                        <span id="btnText">Cari Data</span>
                        <span id="btnLoading" class="spinner-border spinner-border-sm ms-2 d-none" role="status" aria-hidden="true"></span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-7">
        <div class="card border-0 shadow-sm rounded-4 d-none" id="resultCard">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
                    <h5 class="fw-bold m-0" id="resNama">-</h5>
                    <span class="badge rounded-pill" id="resStatus" style="font-size: 0.8rem;">-</span>
                </div>

                <div class="row mb-3">
                    <div class="col-sm-4 text-muted small fw-semibold">Nomor Kartu</div>
                    <div class="col-sm-8 fw-medium" id="resNoKartu">-</div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-4 text-muted small fw-semibold">NIK</div>
                    <div class="col-sm-8 fw-medium" id="resNik">-</div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-4 text-muted small fw-semibold">Jenis Kelamin</div>
                    <div class="col-sm-8 fw-medium" id="resSex">-</div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-4 text-muted small fw-semibold">Jenis Peserta</div>
                    <div class="col-sm-8 fw-medium" id="resJenisPeserta">-</div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-4 text-muted small fw-semibold">Hak Kelas</div>
                    <div class="col-sm-8 fw-medium" id="resKelas">-</div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-4 text-muted small fw-semibold">Faskes Tingkat 1</div>
                    <div class="col-sm-8 fw-medium" id="resFaskes">-</div>
                </div>
            </div>
        </div>

        {{-- Alert untuk pesan error --}}
        <div class="alert alert-danger d-none rounded-4 shadow-sm" id="errorCard" role="alert">
            <div class="d-flex align-items-center">
                <i class="bi bi-exclamation-triangle-fill fs-4 me-3"></i>
                <div id="errorMessage"></div>
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
            .then(response => response.json())
            .then(data => {
                // Kembalikan tombol ke state semula
                btnText.innerText = 'Cari Data';
                btnLoading.classList.add('d-none');
                btnCari.removeAttribute('disabled');

                // Jika Code BPJS adalah 200 (Sukses)
                if (data.metaData && data.metaData.code == "200") {
                    let peserta = data.response.peserta;

                    document.getElementById('resNama').innerText = peserta.nama;
                    document.getElementById('resNoKartu').innerText = peserta.noKartu;
                    document.getElementById('resNik').innerText = peserta.nik;
                    document.getElementById('resSex').innerText = (peserta.sex == 'L') ? 'Laki-laki' : 'Perempuan';
                    document.getElementById('resJenisPeserta').innerText = peserta.jenisPeserta.keterangan;
                    document.getElementById('resKelas').innerText = peserta.hakKelas.keterangan;
                    document.getElementById('resFaskes').innerText = peserta.provUmum.nmProvider;

                    // Mengatur warna badge Status Aktif/Tidak Aktif
                    let statusBadge = document.getElementById('resStatus');
                    statusBadge.innerText = peserta.statusPeserta.keterangan;
                    if (peserta.statusPeserta.kode == "0") { // 0 biasanya Aktif
                        statusBadge.className = "badge rounded-pill bg-success";
                    } else {
                        statusBadge.className = "badge rounded-pill bg-danger";
                    }

                    resultCard.classList.remove('d-none');
                } else {
                    // Tampilkan Error dari BPJS
                    document.getElementById('errorMessage').innerHTML = `<strong>Gagal!</strong><br>${data.metaData.message}`;
                    errorCard.classList.remove('d-none');
                }
            })
            .catch(error => {
                btnText.innerText = 'Cari Data';
                btnLoading.classList.add('d-none');
                btnCari.removeAttribute('disabled');
                
                // Ini akan memunculkan pesan error aslinya di layar
                document.getElementById('errorMessage').innerHTML = `<strong>Terjadi Kesalahan Sistem!</strong><br>Error: ${error.message} <br><br><i>Cek Console Browser (F12) atau laravel.log untuk detailnya.</i>`;
                errorCard.classList.remove('d-none');
                console.error(error);
            });
    });
</script>
@endsection