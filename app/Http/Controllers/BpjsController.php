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
            // 1. CARI PESERTA
            if (strlen($keyword) === 16) {
                $result = $this->bpjs->searchPesertaByNik($keyword, $tglSep);
            } else {
                $result = $this->bpjs->searchPesertaByKartu($keyword, $tglSep);
            }

            // 2. JIKA PESERTA DITEMUKAN, CARI RUJUKAN
            if (isset($result['metaData']['code']) && $result['metaData']['code'] == '200') {
                
                // Gunakan null coalescing (??) agar PHP 8 tidak crash jika BPJS merespon aneh
                $peserta = $result['response']['peserta'] ?? null;
                
                if ($peserta && isset($peserta['noKartu'])) {
                    $noKartuBpjs = $peserta['noKartu'];
                    
                    // Ambil Rujukan
                    $rujukanPcare = $this->bpjs->searchRujukanByNokaPcare($noKartuBpjs);
                    $rujukanRS    = $this->bpjs->searchRujukanByNokaRS($noKartuBpjs);

                    // Pengecekan aman data Rujukan PCare
                    $result['response']['rujukan_pcare'] = 
                        (isset($rujukanPcare['metaData']['code']) && $rujukanPcare['metaData']['code'] == '200') 
                        ? ($rujukanPcare['response']['rujukan'] ?? null) 
                        : null;
                    
                    // Pengecekan aman data Rujukan RS
                    $result['response']['rujukan_rs'] = 
                        (isset($rujukanRS['metaData']['code']) && $rujukanRS['metaData']['code'] == '200') 
                        ? ($rujukanRS['response']['rujukan'] ?? null) 
                        : null;
                }
            }

            return response()->json($result);

        // Menggunakan \Throwable untuk menangkap SEMUA jenis error (termasuk Fatal & TypeError di PHP 8)
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