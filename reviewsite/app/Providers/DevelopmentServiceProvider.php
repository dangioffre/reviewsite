<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use GuzzleHttp\Client;

class DevelopmentServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Only in development environment
        if (config('app.env') === 'local' && env('DISABLE_SSL_VERIFICATION', false)) {
            $this->disableSSLVerification();
        }
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Disable SSL verification for development
     */
    private function disableSSLVerification(): void
    {
        // Disable SSL verification for Guzzle HTTP client
        $this->app->bind(Client::class, function () {
            return new Client([
                'verify' => false,
                'timeout' => 30,
            ]);
        });

        // Set cURL options globally
        if (function_exists('curl_setopt_array')) {
            $curlDefaults = [
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_TIMEOUT => 30,
            ];
            
            // This will be used by Laravel's HTTP client
            config(['http.options' => $curlDefaults]);
        }
    }
}