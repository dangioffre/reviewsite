<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

class TestStreamerController extends Controller
{
    /**
     * Test OAuth configuration for streaming platforms
     */
    public function testOAuth(Request $request)
    {
        $results = [];
        
        // Test each platform configuration
        $platforms = ['twitch', 'youtube', 'kick'];
        
        foreach ($platforms as $platform) {
            try {
                // Check if environment variables are set
                $clientId = config("services.{$platform}.client_id");
                $clientSecret = config("services.{$platform}.client_secret");
                $redirectUri = config("services.{$platform}.redirect");
                
                $results[$platform] = [
                    'configured' => !empty($clientId) && !empty($clientSecret),
                    'client_id' => $clientId ? 'Set' : 'Missing',
                    'client_secret' => $clientSecret ? 'Set' : 'Missing',
                    'redirect_uri' => $redirectUri,
                    'socialite_driver' => 'Available'
                ];
                
                // Test if Socialite can create the driver
                try {
                    $driver = Socialite::driver($platform);
                    $results[$platform]['socialite_driver'] = 'Working';
                } catch (\Exception $e) {
                    $results[$platform]['socialite_driver'] = 'Error: ' . $e->getMessage();
                }
                
            } catch (\Exception $e) {
                $results[$platform] = [
                    'configured' => false,
                    'error' => $e->getMessage()
                ];
            }
        }
        
        return response()->json([
            'status' => 'OAuth Configuration Test',
            'platforms' => $results,
            'recommendations' => $this->getRecommendations($results)
        ], 200, [], JSON_PRETTY_PRINT);
    }
    
    private function getRecommendations(array $results): array
    {
        $recommendations = [];
        
        foreach ($results as $platform => $config) {
            if (!($config['configured'] ?? false)) {
                $recommendations[] = "Configure {$platform} OAuth credentials in your .env file";
            }
        }
        
        if (empty($recommendations)) {
            $recommendations[] = "All platforms are configured! You can now test the OAuth flow.";
        }
        
        return $recommendations;
    }
}