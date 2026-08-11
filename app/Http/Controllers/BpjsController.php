<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\BpjsService;

class BpjsController extends Controller
{
    protected $bpjs;

    public function __construct(BpjsService $bpjs)
    {
        $this->bpjs = $bpjs;
    }

    public function index()
    {
        return view('bpjs.cek-peserta');
    }

    public function cariPeserta(Request $request)
    {
        $input = (string) $request->input('no_kartu');
        $keyword = preg_replace('/[^0-9]/', '', $input); 
        $tglSep = date('Y-m-d'); 

        if (empty($keyword)) {
            return response()->json([
                'metaData' => ['code' => 400, 'message' => 'Nomor Kartu / NIK tidak boleh kosong']
            ], 400);
        }

        try {
            // 1. CARI PESERTA (NIK atau No Kartu)
            if (strlen($keyword) === 16) {
                $result = $this->bpjs->searchPesertaByNik($keyword, $tglSep);
            } else {
                $result = $this->bpjs->searchPesertaByKartu($keyword, $tglSep);
            }

            // 2. JIKA PESERTA DITEMUKAN, TARIK DATA PENDUKUNG
            if (isset($result['metaData']['code']) && $result['metaData']['code'] == '200') {
                $peserta = $result['response']['peserta'] ?? null;
                
                if ($peserta && isset($peserta['noKartu'])) {
                    $noKartuBpjs = $peserta['noKartu'];
                    
                    // A. Rujukan PCare & RS
                    $rujukanPcare = $this->bpjs->searchRujukanByNokaPcare($noKartuBpjs);
                    $rujukanRS    = $this->bpjs->searchRujukanByNokaRS($noKartuBpjs);

                    $result['response']['rujukan_pcare'] = (isset($rujukanPcare['metaData']['code']) && $rujukanPcare['metaData']['code'] == '200') ? ($rujukanPcare['response']['rujukan'] ?? null) : null;
                    $result['response']['rujukan_rs']    = (isset($rujukanRS['metaData']['code']) && $rujukanRS['metaData']['code'] == '200') ? ($rujukanRS['response']['rujukan'] ?? null) : null;

                    // B. Histori Pelayanan / SEP (90 hari ke belakang)
                    $sDate = date('Y-m-d', strtotime('-90 days'));
                    $eDate = date('Y-m-d');
                    $histori = $this->bpjs->monitoringHistoryPelayananPeserta($noKartuBpjs, $sDate, $eDate);
                    
                    $historiList = (isset($histori['metaData']['code']) && $histori['metaData']['code'] == '200') ? ($histori['response']['histori'] ?? []) : [];
                    
                    // Tambahkan detail SEP & noRujukan untuk setiap histori kunjungan agar informatif
                    foreach ($historiList as &$h) {
                        if (!empty($h['noSep'])) {
                            $detailSep = $this->bpjs->searchDetailSep($h['noSep']);
                            if (isset($detailSep['metaData']['code']) && $detailSep['metaData']['code'] == '200') {
                                $h['noRujukan'] = $detailSep['response']['noRujukan'] ?? null;
                                $h['detail_sep'] = $detailSep['response'] ?? null;
                            }
                        }
                    }
                    unset($h);

                    $result['response']['histori_pelayanan'] = $historiList;

                    // C. LIST SURAT KONTROL (2 Bulan Lalu, Bulan Ini, 2 Bulan Kedepan)
                    $allKontrol = [];
                    for ($i = -2; $i <= 2; $i++) {
                        $targetDate = now()->addMonths($i);
                        $bulan = $targetDate->format('m');
                        $tahun = $targetDate->format('Y');

                        $kontrol = $this->bpjs->listKontrolByNoka($bulan, $tahun, $noKartuBpjs, '2');
                        if (isset($kontrol['metaData']['code']) && $kontrol['metaData']['code'] == '200' && !empty($kontrol['response']['list'])) {
                            $allKontrol = array_merge($allKontrol, $kontrol['response']['list']);
                        }
                    }

                    // Hapus duplikat berdasarkan nomor surat kontrol, lalu urutkan berdasarkan tanggal rencana
                    $result['response']['list_kontrol'] = collect($allKontrol)
                        ->unique('noSuratKontrol')
                        ->sortBy('tglRencanaKontrol')
                        ->values()
                        ->all();
                }
            }

            return response()->json($result);

        } catch (\Throwable $e) { 
            return response()->json([
                'metaData' => [
                    'code' => 500,
                    'message' => 'Error Backend: ' . $e->getMessage() . ' (Baris: ' . $e->getLine() . ')'
                ]
            ], 500);
        }
    }

    public function updateKontrolDate(Request $request)
    {
        try {
            $noSuratKontrol = $request->input('noSuratKontrol');
            $newDate = $request->input('tglRencanaKontrol');
            $user = auth()->user()->name ?? 'System';

            // 1. Ambil data asli terlebih dahulu
            $detail = $this->bpjs->getDetailKontrol($noSuratKontrol);

            if (!isset($detail['response']) || empty($detail['response'])) {
                return response()->json([
                    'metaData' => [
                        'code' => 404, 
                        'message' => 'Detail surat kontrol tidak ditemukan di server BPJS'
                    ]
                ], 404);
            }

            $dataLama = $detail['response'];

            // 2. Susun payload sesuai aturan V2 Update
            $payload = [
                "request" => [
                    "noSuratKontrol" => $noSuratKontrol,
                    "noSEP"          => $dataLama['noSepAsalKontrol'] ?? '',
                    "kodeDokter"     => $dataLama['kodeDokter'] ?? '',
                    "poliKontrol"    => $dataLama['poliTujuan'] ?? '',
                    "tglRencanaKontrol" => $newDate,
                    "user"           => $user
                ]
            ];

            // 3. Kirim Update ke BPJS
            $result = $this->bpjs->updateSuratKontrolV2($payload);

            return response()->json($result);

        } catch (\Throwable $e) {
            // INI PENTING: Memastikan error backend apapun dikembalikan dalam bentuk JSON, bukan HTML!
            return response()->json([
                'metaData' => [
                    'code' => 500,
                    'message' => 'Error Backend: ' . $e->getMessage() . ' (Baris: ' . $e->getLine() . ')'
                ]
            ], 500);
        }
    }
}