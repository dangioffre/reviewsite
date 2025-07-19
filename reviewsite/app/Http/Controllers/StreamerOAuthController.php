<?php

namespace App\Http\Controllers;

use App\Services\StreamerOAuthService;
use App\Services\StreamerErrorHandlingService;
use App\Exceptions\OAuthException;
use App\Exceptions\StreamerProfileException;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Exception;

class StreamerOAuthController extends Controller
{
    public function __construct(
        private StreamerOAuthService $oauthService,
        private StreamerErrorHandlingService $errorHandler
    ) {
        //
    }

    /**
     * Redirect to OAuth provider
     *
     * @param string $platform
     * @return RedirectResponse
     */
    public function redirect(string $platform): RedirectResponse
    {
        try {
            $user = Auth::user();
            $redirectUrl = $this->oauthService->initiateConnection($platform, $user);
            
            return redirect($redirectUrl);
        } catch (StreamerProfileException $e) {
            $userMessage = $this->errorHandler->getUserFriendlyMessage($e);
            return redirect()->route('dashboard')
                ->with('error', $userMessage);
        } catch (Exception $e) {
            return redirect()->route('dashboard')
                ->with('error', 'An unexpected error occurred while connecting to ' . ucfirst($platform) . '. Please try again.');
        }
    }

    /**
     * Handle OAuth callback
     *
     * @param string $platform
     * @param Request $request
     * @return RedirectResponse
     */
    public function callback(string $platform, Request $request): RedirectResponse
    {
        try {
            // Validate that we have the required parameters
            if (!$request->has('code')) {
                throw OAuthException::providerError($platform, 'Authorization code not provided');
            }

            // Handle OAuth error responses
            if ($request->has('error')) {
                $error = $request->get('error');
                $errorDescription = $request->get('error_description', $error);
                throw OAuthException::providerError($platform, $errorDescription);
            }

            // Get current authenticated user
            $user = Auth::user();
            if (!$user) {
                return redirect()->route('login')
                    ->with('error', 'You must be logged in to connect a streaming platform.');
            }

            // Handle the OAuth callback
            $streamerProfile = $this->oauthService->handleCallback(
                $platform, 
                $request->get('code'), 
                $user
            );

            // Clear OAuth session data
            session()->forget(['oauth_user_id', 'oauth_platform']);

            return redirect()->route('streamer.profile.show', $streamerProfile)
                ->with('success', "Successfully connected your {$platform} account! Your streamer profile is now active and ready to use.");

        } catch (StreamerProfileException $e) {
            // Clear OAuth session data on error
            session()->forget(['oauth_user_id', 'oauth_platform']);

            $userMessage = $this->errorHandler->getUserFriendlyMessage($e);
            return redirect()->route('dashboard')
                ->with('error', $userMessage);
        } catch (Exception $e) {
            // Clear OAuth session data on error
            session()->forget(['oauth_user_id', 'oauth_platform']);

            return redirect()->route('dashboard')
                ->with('error', "An unexpected error occurred while connecting your {$platform} account. Please try again.");
        }
    }

    /**
     * Disconnect OAuth connection
     *
     * @param string $platform
     * @return RedirectResponse
     */
    public function disconnect(string $platform): RedirectResponse
    {
        try {
            $user = Auth::user();
            $streamerProfile = $user->streamerProfile()
                ->where('platform', $platform)
                ->firstOrFail();

            $success = $this->oauthService->revokeConnection($streamerProfile);

            if ($success) {
                return redirect()->route('dashboard')
                    ->with('success', "Successfully disconnected your {$platform} account.");
            } else {
                return redirect()->route('dashboard')
                    ->with('warning', "Your {$platform} account has been disconnected locally, but there may have been an issue revoking the connection with {$platform}.");
            }

        } catch (StreamerProfileException $e) {
            $userMessage = $this->errorHandler->getUserFriendlyMessage($e);
            return redirect()->route('dashboard')
                ->with('error', $userMessage);
        } catch (Exception $e) {
            return redirect()->route('dashboard')
                ->with('error', "An unexpected error occurred while disconnecting your {$platform} account. Please try again.");
        }
    }
}