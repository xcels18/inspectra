<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use Masbug\Flysystem\GoogleDriveAdapter;
use Google\Client as GoogleClient;
use Google\Service\Drive;
use League\Flysystem\Filesystem;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();

        Storage::extend('google', function ($app, $config) {
            $client = new GoogleClient();
            $client->setAuthConfig(base_path($config['serviceAccountCredentials']));
            $client->addScope(Drive::DRIVE);

            $service = new Drive($client);
            $adapter = new GoogleDriveAdapter($service, null, [
                'useDisplayPaths'  => true,
                'sharedFolderId'   => $config['folder'],
            ]);

            return new \Illuminate\Filesystem\FilesystemAdapter(
                new Filesystem($adapter),
                $adapter
            );
        });
    }
}
