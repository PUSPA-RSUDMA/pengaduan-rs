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

    // 2. Fungsi untuk memproses pencarian berdasarkan No Kartu
    public function cariPeserta(Request $request)
    {
        $noKartu = $request->input('no_kartu');
        $tglSep = date('Y-m-d'); // Menggunakan tanggal hari ini untuk pengecekan aktif/tidaknya kartu

        if (!$noKartu) {
            return response()->json([
                'metaData' => [
                    'code' => 400, 
                    'message' => 'Nomor Kartu tidak boleh kosong'
                ]
            ], 400);
        }

        // Memanggil fungsi dari BpjsService (pastikan fungsi ini ada di service Anda)
        $result = $this->bpjs->searchPesertaByKartu($noKartu, $tglSep);

        return response()->json($result);
    }
}