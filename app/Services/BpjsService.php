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

    /**
     * Generate Headers & Signature BPJS
     */
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

    /**
     * Decrypt Response BPJS
     */
    private function decryptResponse($encryptedResponse, $timestamp)
    {
        $key = $this->consId . $this->secretKey . $timestamp;
        $hash = hash('sha256', $key, true);
        
        $key_hash = substr($hash, 0, 32);
        $iv = substr($hash, 0, 16);

        // AES Decrypt
        $decrypted = openssl_decrypt(base64_decode($encryptedResponse), 'AES-256-CBC', $key_hash, OPENSSL_RAW_DATA, $iv);
        
        // LZString Decompress
        $lz = new LZString();
        $decompressed = $lz->decompressFromEncodedURIComponent($decrypted);
        
        return json_decode($decompressed, true);
    }

    /**
     * Core Request Method (Pengganti __requestServiceVCLAIM di Python)
     */
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

            // Handle metadata consistency
            if (isset($result['metadata'])) {
                $result['metaData'] = $result['metadata'];
                unset($result['metadata']);
            }

            // TODO: Tambahkan logic Insert Log ke Database di sini (seperti VclaimLogs::create di Python)
            // Contoh: VclaimLog::create(['endpoint' => $endpoint, 'response_code' => $result['metaData']['code']]);

            if (isset($result['metaData']['code']) && $result['metaData']['code'] == '200' && isset($result['response']) && $result['response'] !== null) {
                // Lakukan Dekripsi
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

    // =========================================================================
    // DAFTAR ENDPOINT (Bisa Anda tambahkan sesuai kebutuhan dari Python)
    // =========================================================================

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
}