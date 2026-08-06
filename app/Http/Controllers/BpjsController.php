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
    // 2. Fungsi untuk memproses pencarian berdasarkan No Kartu ATAU NIK
    public function cariPeserta(Request $request)
    {
        // Ambil input dan bersihkan dari spasi atau karakter selain angka
        $keyword = preg_replace('/[^0-9]/', '', $request->input('no_kartu')); 
        $tglSep = date('Y-m-d'); // Tanggal hari ini

        if (empty($keyword)) {
            return response()->json([
                'metaData' => [
                    'code' => 400, 
                    'message' => 'Nomor Kartu / NIK tidak boleh kosong'
                ]
            ], 400);
        }

        // AUTO-DETECT LOGIC (Sangat simpel untuk pengguna)
        if (strlen($keyword) === 16) {
            // Jika 16 Digit -> Arahkan ke endpoint pencarian NIK
            $result = $this->bpjs->searchPesertaByNik($keyword, $tglSep);
        } else {
            // Selain 16 Digit (biasanya 13 digit) -> Arahkan ke endpoint pencarian No Kartu
            $result = $this->bpjs->searchPesertaByKartu($keyword, $tglSep);
        }

        return response()->json($result);
    }
}