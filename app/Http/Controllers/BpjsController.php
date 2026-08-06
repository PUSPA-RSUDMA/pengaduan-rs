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

    // 1. Fungsi untuk menampilkan halaman view
    public function index()
    {
        return view('bpjs.cek-peserta');
    }

    public function cariPeserta(Request $request)
    {
        // PERBAIKAN PHP 8.4: Tambahkan (string) agar terhindar dari TypeError
        $input = (string) $request->input('no_kartu');
        $keyword = preg_replace('/[^0-9]/', '', $input); 
        
        $tglSep = date('Y-m-d'); 

        if (empty($keyword)) {
            return response()->json([
                'metaData' => [
                    'code' => 400, 
                    'message' => 'Nomor Kartu / NIK tidak valid'
                ]
            ], 400);
        }

        // Cek Panjang Karakter
        if (strlen($keyword) === 16) {
            $result = $this->bpjs->searchPesertaByNik($keyword, $tglSep);
        } else {
            $result = $this->bpjs->searchPesertaByKartu($keyword, $tglSep);
        }

        return response()->json($result);
    }
}