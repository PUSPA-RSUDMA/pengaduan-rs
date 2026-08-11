<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use LZCompressor\LZString;

class BpjsService
{
    protected $baseUrl;
    protected $consId;
    protected $secretKey;
    protected $userKey;

    public function __construct()
    {
        $this->baseUrl = config('bpjs.vclaim.base_url');
        $this->consId = config('bpjs.vclaim.cons_id');
        $this->secretKey = config('bpjs.vclaim.secret_key');
        $this->userKey = config('bpjs.vclaim.user_key');
    }

    private function generateHeader()
    {
        date_default_timezone_set('UTC');
        $timestamp = strval(time() - strtotime('1970-01-01 00:00:00'));
        $signature = hash_hmac('sha256', $this->consId . "&" . $timestamp, $this->secretKey, true);
        $encodedSignature = base64_encode($signature);

        return [
            'X-cons-id'   => $this->consId,
            'X-timestamp' => $timestamp,
            'X-signature' => $encodedSignature,
            'user_key'    => $this->userKey,
            'Content-Type'=> 'application/json; charset=utf-8'
        ];
    }

    private function decryptResponse($encryptedResponse, $timestamp)
    {
        $key = $this->consId . $this->secretKey . $timestamp;
        $hash = hash('sha256', $key, true);
        
        $key_hash = substr($hash, 0, 32);
        $iv = substr($hash, 0, 16);

        $decrypted = openssl_decrypt(base64_decode($encryptedResponse), 'AES-256-CBC', $key_hash, OPENSSL_RAW_DATA, $iv);
        
        $lz = new LZString();
        $decompressed = $lz->decompressFromEncodedURIComponent($decrypted);
        
        return json_decode($decompressed, true);
    }

    private function request($endpoint, $method = 'GET', $data = [])
    {
        $headers = $this->generateHeader();
        $url = $this->baseUrl . ltrim($endpoint, '/');

        try {
            if ($method === 'GET') {
                $response = Http::withHeaders($headers)->timeout(30)->get($url);
            } elseif ($method === 'POST') {
                $response = Http::withHeaders($headers)->timeout(30)->post($url, $data);
            } elseif ($method === 'PUT') {
                $response = Http::withHeaders($headers)->timeout(30)->put($url, $data);
            } elseif ($method === 'DELETE') {
                $response = Http::withHeaders($headers)->timeout(30)->delete($url, $data);
            }

            $result = $response->json();

            if (isset($result['metadata'])) {
                $result['metaData'] = $result['metadata'];
                unset($result['metadata']);
            }

            if (isset($result['metaData']['code']) && $result['metaData']['code'] == '200' && isset($result['response']) && $result['response'] !== null) {
                $result['response'] = $this->decryptResponse($result['response'], $headers['X-timestamp']);
            }

            return $result;

        } catch (\Exception $e) {
            Log::error("BPJS API Error: " . $e->getMessage());
            return [
                'metaData' => [
                    'code' => 500,
                    'message' => $e->getMessage()
                ],
                'response' => null
            ];
        }
    }

    // ==========================================
    // ENDPOINT VCLAIM BPJS
    // ==========================================

    public function searchPesertaByNik($nik, $tglSep)
    {
        return $this->request("/Peserta/nik/{$nik}/tglSEP/{$tglSep}", 'GET');
    }

    public function searchPesertaByKartu($noka, $tglSep)
    {
        return $this->request("/Peserta/nokartu/{$noka}/tglSEP/{$tglSep}", 'GET');
    }

    public function searchRujukanByNokaPcare($noka)
    {
        return $this->request("/Rujukan/Peserta/{$noka}", 'GET');
    }

    public function searchRujukanByNokaRS($noka)
    {
        return $this->request("/Rujukan/RS/Peserta/{$noka}", 'GET');
    }

    public function monitoringHistoryPelayananPeserta($noka, $sDate, $eDate)
    {
        return $this->request("/monitoring/HistoriPelayanan/NoKartu/{$noka}/tglMulai/{$sDate}/tglAkhir/{$eDate}", 'GET');
    }

    public function listKontrolByNoka($bulan, $tahun, $noka, $filter = 1)
    {
        return $this->request("/RencanaKontrol/ListRencanaKontrol/Bulan/{$bulan}/Tahun/{$tahun}/Nokartu/{$noka}/filter/{$filter}", 'GET');
    }

    // Tambahan Baru: List Rencana Kontrol Berdasarkan Bulan (Tanpa Noka)
    public function listKontrolBulanan($bulan, $tahun)
    {
        return $this->request("/RencanaKontrol/ListRencanaKontrol/Bulan/{$bulan}/Tahun/{$tahun}", 'GET');
    }

    // Tambahan Baru: Update Surat Kontrol V2 (Mengabaikan formPRB)
    public function updateSuratKontrolV2($data)
    {
        if (isset($data['request']['formPRB'])) {
            unset($data['request']['formPRB']);
        }
        return $this->request("/RencanaKontrol/v2/Update", 'PUT', $data);
    }

    // Tambahan Baru: Pencarian Detail SEP (Memunculkan noRujukan, poli, peserta, dll)
    public function searchDetailSep($noSep)
    {
        return $this->request("/SEP/{$noSep}", 'GET');
    }

    // Tambahan Baru: Pencarian Rujukan Berdasarkan Nomor Rujukan PCare/RS untuk melacak rujukan ke SEP apa saja
    public function searchRujukanByNoRujukan($noRujukan, $isRs = false)
    {
        $prefix = $isRs ? "/Rujukan/RS" : "/Rujukan";
        return $this->request("{$prefix}/{$noRujukan}", 'GET');
    }
}