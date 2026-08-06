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

            // 2. JIKA PESERTA DITEMUKAN, TARIK DATA PENDUKUNG (Rujukan, Histori SEP, Surat Kontrol)
            if (isset($result['metaData']['code']) && $result['metaData']['code'] == '200') {
                $peserta = $result['response']['peserta'] ?? null;
                
                if ($peserta && isset($peserta['noKartu'])) {
                    $noKartuBpjs = $peserta['noKartu'];
                    
                    // A. Rujukan PCare & RS
                    $rujukanPcare = $this->bpjs->searchRujukanByNokaPcare($noKartuBpjs);
                    $rujukanRS    = $this->bpjs->searchRujukanByNokaRS($noKartuBpjs);

                    $result['response']['rujukan_pcare'] = (isset($rujukanPcare['metaData']['code']) && $rujukanPcare['metaData']['code'] == '200') ? ($rujukanPcare['response']['rujukan'] ?? null) : null;
                    $result['response']['rujukan_rs']    = (isset($rujukanRS['metaData']['code']) && $rujukanRS['metaData']['code'] == '200') ? ($rujukanRS['response']['rujukan'] ?? null) : null;

                    // B. Histori Pelayanan / SEP (Rentang 90 hari ke belakang sampai hari ini)
                    $sDate = date('Y-m-d', strtotime('-90 days'));
                    $eDate = date('Y-m-d');
                    $histori = $this->bpjs->monitoringHistoryPelayananPeserta($noKartuBpjs, $sDate, $eDate);
                    
                    $result['response']['histori_pelayanan'] = (isset($histori['metaData']['code']) && $histori['metaData']['code'] == '200') ? ($histori['response']['histori'] ?? []) : [];

                    // C. List Surat Kontrol (Bulan & Tahun aktif saat ini, filter 2 untuk semua jenis poli atau 1 sesuai kebutuhan)
                    $bulan = date('m');
                    $tahun = date('Y');
                    $kontrol = $this->bpjs->listKontrolByNoka($bulan, $tahun, $noKartuBpjs, '2');

                    $result['response']['list_kontrol'] = (isset($kontrol['metaData']['code']) && $kontrol['metaData']['code'] == '200') ? ($kontrol['response']['list'] ?? []) : [];
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
}