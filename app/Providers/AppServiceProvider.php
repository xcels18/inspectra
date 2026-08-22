<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use Masbug\Flysystem\GoogleDriveAdapter;
use Google\Client as GoogleClient;
use Google\Service\Drive;
use League\Flysystem\Filesystem;
use App\Models\Setting;

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
            
            // Override configs with database settings if available
            try {
                $credentialsPath = Setting::get('gdrive_credentials_path');
                $folderId = Setting::get('gdrive_folder_id');
                
                if ($credentialsPath) {
                    $config['serviceAccountCredentials'] = $credentialsPath;
                }
                if ($folderId) {
                    $config['folder'] = $folderId;
                }
            } catch (\Exception $e) {
                // Ignore if DB is not ready
            }

            if (!empty($config['serviceAccountCredentials'])) {
                $authPath = $config['serviceAccountCredentials'];
                // If it's just a filename like 'google-drive.json' inside private storage
                if (file_exists(storage_path('app/private/' . $authPath))) {
                    $authPath = storage_path('app/private/' . $authPath);
                } elseif (!file_exists($authPath)) {
                    // Fallback to base_path if it was a relative path from old .env
                    $authPath = base_path($authPath);
                }
                
                if (file_exists($authPath)) {
                    $client->setAuthConfig($authPath);
                }
            }
            
            $client->addScope(Drive::DRIVE);

            $service = new Drive($client);
            $adapter = new GoogleDriveAdapter($service, null, [
                'useDisplayPaths'  => true,
                'sharedFolderId'   => $config['folder'] ?? '',
            ]);

            return new \Illuminate\Filesystem\FilesystemAdapter(
                new Filesystem($adapter),
                $adapter
            );
        });
    }
}
