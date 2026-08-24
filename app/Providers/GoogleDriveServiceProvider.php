<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\Filesystem;
use Masbug\Flysystem\GoogleDriveAdapter;
use Illuminate\Filesystem\FilesystemAdapter;
  
class GoogleDriveServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Storage::extend('google', function($app, $config) {
            $options = $config['options'] ?? [];

            $client = new \Google\Client();
            $client->setClientId($config['clientId']);
            $client->setClientSecret($config['clientSecret']);
            
            // 1. Ambil token dan simpan responnya ke dalam variabel $token
            $token = $client->fetchAccessTokenWithRefreshToken($config['refreshToken']);
            
            // 2. CEK ERROR: Jika Google menolak, hentikan aplikasi dan tampilkan alasannya!
            if (isset($token['error'])) {
                dd(
                    'GAGAL MENDAPATKAN AKSES DARI GOOGLE!', 
                    'Alasan Error: ' . $token['error'],
                    'Deskripsi: ' . ($token['error_description'] ?? 'Tidak ada detail'),
                    'Refresh Token yang terbaca oleh sistem: ' . $config['refreshToken']
                );
            }

            $service = new \Google\Service\Drive($client);
            $adapter = new GoogleDriveAdapter($service, $config['folder'] ?? '/', $options);

            $driver = new Filesystem($adapter, $config);

            return new FilesystemAdapter($driver, $adapter, $config);
        });
    }
}