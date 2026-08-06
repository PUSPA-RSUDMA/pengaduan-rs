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
        // 1. Ambil input dan paksakan menjadi string agar terhindar dari TypeError
        $input = (string) $request->input('no_kartu');
        
        // 2. Bersihkan karakter spasi/simbol, hanya sisakan angka
        $keyword = preg_replace('/[^0-9]/', '', $input); 
        $tglSep = date('Y-m-d'); 

        // 3. Validasi kosong
        if (empty($keyword)) {
            return response()->json([
                'metaData' => [
                    'code' => 400, 
                    'message' => 'Nomor Kartu / NIK tidak boleh kosong'
                ]
            ], 400);
        }

        try {
            // 4. AUTO-DETECT: 16 Digit = NIK, selain itu = No Kartu BPJS
            if (strlen($keyword) === 16) {
                $result = $this->bpjs->searchPesertaByNik($keyword, $tglSep);
            } else {
                $result = $this->bpjs->searchPesertaByKartu($keyword, $tglSep);
            }

            return response()->json($result);

        } catch (\Exception $e) {
            // Menangkap error jika Service gagal dipanggil
            return response()->json([
                'metaData' => [
                    'code' => 500,
                    'message' => 'Error Internal: ' . $e->getMessage()
                ]
            ], 500);
        }
    }
}