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
        
        // Aturan Khusus BPJS: Header Content-Type harus x-www-form-urlencoded untuk metode Insert/Update
        if (in_array($method, ['POST', 'PUT', 'DELETE'])) {
            $headers['Content-Type'] = 'Application/x-www-form-urlencoded';
        }

        $url = $this->baseUrl . ltrim($endpoint, '/');

        try {
            if ($method === 'GET') {
                $response = Http::withHeaders($headers)->timeout(30)->get($url);
            } else {
                // Aturan Khusus BPJS: Body harus berupa JSON String murni
                $response = Http::withHeaders($headers)
                    ->withBody(json_encode($data), 'Application/x-www-form-urlencoded')
                    ->send($method, $url);
            }

            // Catat RAW Body untuk Debugging 
            Log::info("RAW Body dari BPJS [{$method} {$url}]", [
                'status_code' => $response->status(),
                'body_mentah' => $response->body()
            ]);

            $result = $response->json();

            // Jika hasil bukan JSON (misal HTML 404/500 dari IIS Server)
            if ($result === null) {
                return [
                    'metaData' => [
                        'code' => $response->status(),
                        'message' => 'Respon BPJS BUKAN Json. Cek log RAW Body!'
                    ],
                    'response' => null
                ];
            }

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

    public function listKontrolBulanan($bulan, $tahun)
    {
        return $this->request("/RencanaKontrol/ListRencanaKontrol/Bulan/{$bulan}/Tahun/{$tahun}", 'GET');
    }

    public function searchDetailSep($noSep)
    {
        return $this->request("/SEP/{$noSep}", 'GET');
    }
    
    public function getDetailKontrol($noSuratKontrol)
    {
        return $this->request("/RencanaKontrol/noSuratKontrol/{$noSuratKontrol}", 'GET');
    }

    public function updateSuratKontrolV2($data)
    {
        // Path asli API BPJS untuk update Surat Kontrol
        return $this->request("/RencanaKontrol/Update", 'PUT', $data);
    }
}