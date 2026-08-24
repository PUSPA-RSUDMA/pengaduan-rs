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

            \Illuminate\Support\Facades\Log::info('Cek Config Google:', $config);

            $client = new \Google\Client();
            $client->setClientId($config['clientId']);
            $client->setClientSecret($config['clientSecret']);
            // $client->refreshToken($config['refreshToken']);
            $client->fetchAccessTokenWithRefreshToken($config['refreshToken']);

            $service = new \Google\Service\Drive($client);
            $adapter = new GoogleDriveAdapter($service, $config['folder'] ?? '/', $options);
            
            $driver = new Filesystem($adapter, $config);

            return new FilesystemAdapter($driver, $adapter, $config);
        });
    }
}