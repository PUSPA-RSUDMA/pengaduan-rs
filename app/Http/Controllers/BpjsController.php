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
                'metaData' => [
                    'code' => 400, 
                    'message' => 'Nomor Kartu / NIK tidak boleh kosong'
                ]
            ], 400);
        }

        try {
            // 1. CEK DATA PESERTA UTAMA
            if (strlen($keyword) === 16) {
                $result = $this->bpjs->searchPesertaByNik($keyword, $tglSep);
            } else {
                $result = $this->bpjs->searchPesertaByKartu($keyword, $tglSep);
            }

            // 2. JIKA PESERTA DITEMUKAN, OTOMATIS CARI RUJUKAN
            if (isset($result['metaData']['code']) && $result['metaData']['code'] == '200') {
                $noKartuBpjs = $result['response']['peserta']['noKartu'];
                
                // Panggil API Rujukan PCare & RS sekaligus
                $rujukanPcare = $this->bpjs->searchRujukanByNokaPcare($noKartuBpjs);
                $rujukanRS    = $this->bpjs->searchRujukanByNokaRS($noKartuBpjs);

                // Sisipkan data rujukan ke dalam JSON yang akan dikirim ke layar (Tampilan)
                $result['response']['rujukan_pcare'] = ($rujukanPcare['metaData']['code'] == '200') ? $rujukanPcare['response']['rujukan'] : null;
                
                $result['response']['rujukan_rs'] = ($rujukanRS['metaData']['code'] == '200') ? $rujukanRS['response']['rujukan'] : null;
            }

            return response()->json($result);

        } catch (\Exception $e) {
            return response()->json([
                'metaData' => [
                    'code' => 500,
                    'message' => 'Error Internal: ' . $e->getMessage()
                ]
            ], 500);
        }
    }
}