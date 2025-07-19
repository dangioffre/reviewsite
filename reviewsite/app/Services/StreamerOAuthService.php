<?php

namespace App\Services;

use App\Models\User;
use App\Models\StreamerProfile;
use App\Exceptions\OAuthException;
use App\Services\StreamerErrorHandlingService;
use App\Services\RetryService;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use Exception;

class StreamerOAuthService
{
    /**
     * Supported streaming platforms
     */
    private const SUPPORTED_PLATFORMS = ['twitch', 'youtube', 'kick'];

    private StreamerErrorHandlingService $errorHandler;
    private RetryService $retryService;

    public function __construct(
        StreamerErrorHandlingService $errorHandler,
        RetryService $retryService
    ) {
        $this->errorHandler = $errorHandler;
        $this->retryService = $retryService;
    }

    /**
     * Initiate OAuth connection for a streaming platform
     *
     * @param string $platform
     * @param User $user
     * @return string Redirect URL
     * @throws OAuthException
     */
    public function initiateConnection(string $platform, User $user): string
    {
        try {
            $this->validatePlatform($platform);
            
            // Check if user already has a profile for this platform
            if ($this->userHasProfileForPlatform($user, $platform)) {
                throw OAuthException::accountAlreadyConnected($platform, ['user_id' => $user->id]);
            }

            return $this->retryService->executeForPlatform(function () use ($platform, $user) {
                // Store user ID in session for callback handling
                session(['oauth_user_id' => $user->id, 'oauth_platform' => $platform]);
                
                $driver = Socialite::driver($platform)
                    ->stateless()
                    ->scopes($this->getPlatformScopes($platform));
                
                // Disable SSL verification for development
                if (env('DISABLE_SSL_VERIFICATION', false)) {
                    $driver->setHttpClient(new \GuzzleHttp\Client([
                        'verify' => false,
                        'timeout' => 30,
                    ]));
                }
                
                return $driver->redirect()->getTargetUrl();
            }, $platform);

        } catch (OAuthException $e) {
            throw $e;
        } catch (Exception $e) {
            $context = ['user_id' => $user->id];
            throw $this->errorHandler->handleOAuthError($e, $platform, $context);
        }
    }

    /**
     * Handle OAuth callback and create streamer profile
     *
     * @param string $platform
     * @param string $code
     * @param User $user
     * @return StreamerProfile
     * @throws OAuthException
     */
    public function handleCallback(string $platform, string $code, User $user): StreamerProfile
    {
        try {
            $this->validatePlatform($platform);

            return $this->retryService->executeForPlatform(function () use ($platform, $user) {
                DB::beginTransaction();

                try {
                    // Get user data from OAuth provider (stateless to avoid state validation issues)
                    $driver = Socialite::driver($platform)->stateless();
                    
                    // Disable SSL verification for development
                    if (env('DISABLE_SSL_VERIFICATION', false)) {
                        $driver->setHttpClient(new \GuzzleHttp\Client([
                            'verify' => false,
                            'timeout' => 30,
                        ]));
                    }
                    
                    $socialiteUser = $driver->user();
                    
                    // Check if this platform account is already connected to another user
                    $existingProfile = StreamerProfile::where('platform', $platform)
                        ->where('platform_user_id', $socialiteUser->getId())
                        ->first();

                    if ($existingProfile && $existingProfile->user_id !== $user->id) {
                        throw OAuthException::accountAlreadyConnected($platform, [
                            'user_id' => $user->id,
                            'existing_user_id' => $existingProfile->user_id
                        ]);
                    }

                    // Create or update streamer profile
                    $profileData = $this->extractProfileData($platform, $socialiteUser);
                    
                    $streamerProfile = StreamerProfile::updateOrCreate(
                        [
                            'user_id' => $user->id,
                            'platform' => $platform,
                        ],
                        array_merge($profileData, [
                            'oauth_token' => $socialiteUser->token,
                            'oauth_refresh_token' => $socialiteUser->refreshToken,
                            'oauth_expires_at' => $socialiteUser->expiresIn ? 
                                now()->addSeconds($socialiteUser->expiresIn) : null,
                            'is_approved' => true, // Auto-approve all OAuth-connected profiles
                            'is_verified' => true, // Auto-verify all OAuth-connected profiles
                            'verification_status' => 'verified',
                            'verification_completed_at' => now(),
                        ])
                    );

                    DB::commit();

                    Log::info("Successfully created/updated {$platform} streamer profile", [
                        'user_id' => $user->id,
                        'profile_id' => $streamerProfile->id,
                        'channel_name' => $streamerProfile->channel_name
                    ]);

                    return $streamerProfile;

                } catch (Exception $e) {
                    DB::rollBack();
                    throw $e;
                }
            }, $platform);

        } catch (OAuthException $e) {
            throw $e;
        } catch (Exception $e) {
            $context = ['user_id' => $user->id, 'code' => substr($code, 0, 10) . '...'];
            throw $this->errorHandler->handleOAuthError($e, $platform, $context);
        }
    }

    /**
     * Refresh OAuth token for a streamer profile
     *
     * @param StreamerProfile $profile
     * @return bool
     */
    public function refreshToken(StreamerProfile $profile): bool
    {
        if (!$profile->oauth_refresh_token) {
            Log::warning("No refresh token available for profile", [
                'profile_id' => $profile->id,
                'platform' => $profile->platform
            ]);
            return false;
        }

        try {
            return $this->retryService->executeForPlatform(function () use ($profile) {
                // Use refresh token to get new access token
                $response = Socialite::driver($profile->platform)
                    ->refreshToken($profile->oauth_refresh_token);

                $profile->update([
                    'oauth_token' => $response->token,
                    'oauth_refresh_token' => $response->refreshToken ?? $profile->oauth_refresh_token,
                    'oauth_expires_at' => $response->expiresIn ? 
                        now()->addSeconds($response->expiresIn) : null,
                ]);

                Log::info("Successfully refreshed OAuth token", [
                    'profile_id' => $profile->id,
                    'platform' => $profile->platform
                ]);

                return true;
            }, $profile->platform);

        } catch (Exception $e) {
            $context = ['profile_id' => $profile->id];
            $oauthException = $this->errorHandler->handleOAuthError($e, $profile->platform, $context);
            
            Log::error("Failed to refresh OAuth token", [
                'profile_id' => $profile->id,
                'platform' => $profile->platform,
                'error' => $oauthException->getMessage()
            ]);
            
            return false;
        }
    }

    /**
     * Revoke OAuth connection and clean up tokens
     *
     * @param StreamerProfile $profile
     * @return bool
     */
    public function revokeConnection(StreamerProfile $profile): bool
    {
        try {
            // Attempt to revoke token with the provider (if supported)
            $this->revokeTokenWithProvider($profile);

            // Clear OAuth tokens from profile
            $profile->update([
                'oauth_token' => null,
                'oauth_refresh_token' => null,
                'oauth_expires_at' => null,
            ]);

            Log::info("Successfully revoked OAuth connection", [
                'profile_id' => $profile->id,
                'platform' => $profile->platform
            ]);

            return true;

        } catch (Exception $e) {
            Log::error("Failed to revoke OAuth connection", [
                'profile_id' => $profile->id,
                'platform' => $profile->platform,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Check if token needs refresh
     *
     * @param StreamerProfile $profile
     * @return bool
     */
    public function tokenNeedsRefresh(StreamerProfile $profile): bool
    {
        if (!$profile->oauth_expires_at) {
            return false;
        }

        // Refresh if token expires within 5 minutes
        return $profile->oauth_expires_at->subMinutes(5)->isPast();
    }

    /**
     * Validate that the platform is supported
     *
     * @param string $platform
     * @throws OAuthException
     */
    private function validatePlatform(string $platform): void
    {
        if (!in_array($platform, self::SUPPORTED_PLATFORMS)) {
            throw OAuthException::unsupportedPlatform($platform);
        }
    }

    /**
     * Check if user already has a profile for the platform
     *
     * @param User $user
     * @param string $platform
     * @return bool
     */
    private function userHasProfileForPlatform(User $user, string $platform): bool
    {
        return $user->streamerProfile()
            ->where('platform', $platform)
            ->exists();
    }

    /**
     * Get required OAuth scopes for each platform
     *
     * @param string $platform
     * @return array
     */
    private function getPlatformScopes(string $platform): array
    {
        return match ($platform) {
            'twitch' => ['user:read:email', 'channel:read:subscriptions'],
            'youtube' => ['https://www.googleapis.com/auth/youtube.readonly'],
            'kick' => [], // Kick may not require specific scopes
            default => [],
        };
    }

    /**
     * Extract profile data from OAuth provider response
     *
     * @param string $platform
     * @param mixed $socialiteUser
     * @return array
     */
    private function extractProfileData(string $platform, $socialiteUser): array
    {
        $baseData = [
            'platform_user_id' => $socialiteUser->getId(),
            'channel_name' => $socialiteUser->getName() ?? $socialiteUser->getNickname(),
            'profile_photo_url' => $socialiteUser->getAvatar(),
        ];

        // Platform-specific data extraction
        switch ($platform) {
            case 'twitch':
                $baseData['channel_url'] = 'https://twitch.tv/' . ($socialiteUser->getNickname() ?? $socialiteUser->getName());
                $baseData['bio'] = $socialiteUser->user['description'] ?? null;
                break;

            case 'youtube':
                // YouTube channel URL needs to be constructed from channel ID
                $baseData['channel_url'] = 'https://youtube.com/channel/' . $socialiteUser->getId();
                $baseData['bio'] = $socialiteUser->user['snippet']['description'] ?? null;
                break;

            case 'kick':
                $baseData['channel_url'] = 'https://kick.com/' . ($socialiteUser->getNickname() ?? $socialiteUser->getName());
                $baseData['bio'] = $socialiteUser->user['bio'] ?? null;
                break;
        }

        return $baseData;
    }

    /**
     * Attempt to revoke token with OAuth provider
     *
     * @param StreamerProfile $profile
     */
    private function revokeTokenWithProvider(StreamerProfile $profile): void
    {
        // This would implement platform-specific token revocation
        // For now, we'll just log the attempt
        Log::info("Attempting to revoke token with provider", [
            'profile_id' => $profile->id,
            'platform' => $profile->platform
        ]);

        // Platform-specific revocation logic would go here
        // Each platform has different revocation endpoints and methods
    }
}