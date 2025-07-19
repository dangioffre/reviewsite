<?php

namespace App\Http\Controllers;

use App\Models\StreamerProfile;
use App\Services\PlatformApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class StreamerVerificationController extends Controller
{
    public function __construct(
        private PlatformApiService $platformApiService
    ) {
        // Middleware is applied in routes
    }

    /**
     * Request verification for a streamer profile.
     */
    public function request(StreamerProfile $streamerProfile)
    {
        // Ensure the user owns this profile
        if ($streamerProfile->user_id !== Auth::id()) {
            abort(403, 'You can only request verification for your own profile.');
        }

        if (!$streamerProfile->canRequestVerification()) {
            return redirect()->back()->with('error', 'Verification cannot be requested at this time.');
        }

        try {
            // Attempt to verify channel ownership through platform API
            $ownershipVerified = $this->platformApiService->validateChannelOwnership($streamerProfile);
            
            if ($ownershipVerified) {
                $streamerProfile->requestVerification();
                
                Log::info('Verification requested for streamer profile', [
                    'profile_id' => $streamerProfile->id,
                    'user_id' => Auth::id(),
                    'platform' => $streamerProfile->platform,
                    'channel_name' => $streamerProfile->channel_name,
                ]);

                return redirect()->back()->with('success', 'Verification request submitted successfully. Our team will review your profile.');
            } else {
                return redirect()->back()->with('error', 'Unable to verify channel ownership. Please ensure your OAuth connection is valid.');
            }
        } catch (\Exception $e) {
            Log::error('Error requesting verification', [
                'profile_id' => $streamerProfile->id,
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()->with('error', 'An error occurred while requesting verification. Please try again later.');
        }
    }

    /**
     * Show verification status page.
     */
    public function show(StreamerProfile $streamerProfile)
    {
        // Debug logging
        Log::info('Verification show access attempt', [
            'current_user_id' => Auth::id(),
            'profile_user_id' => $streamerProfile->user_id,
            'profile_id' => $streamerProfile->id,
            'channel_name' => $streamerProfile->channel_name,
            'match' => $streamerProfile->user_id === Auth::id(),
        ]);

        // Ensure the user owns this profile
        if ($streamerProfile->user_id !== Auth::id()) {
            Log::warning('Verification access denied', [
                'current_user_id' => Auth::id(),
                'profile_user_id' => $streamerProfile->user_id,
                'profile_id' => $streamerProfile->id,
            ]);
            abort(403, 'You can only view your own verification status.');
        }

        return view('streamer.verification.show', ['profile' => $streamerProfile]);
    }

    /**
     * Cancel verification request.
     */
    public function cancel(StreamerProfile $streamerProfile)
    {
        // Ensure the user owns this profile
        if ($streamerProfile->user_id !== Auth::id()) {
            abort(403, 'You can only cancel your own verification request.');
        }

        if ($streamerProfile->verification_status !== 'requested') {
            return redirect()->back()->with('error', 'Cannot cancel verification at this stage.');
        }

        $streamerProfile->update([
            'verification_status' => 'pending',
            'verification_token' => null,
            'verification_requested_at' => null,
        ]);

        return redirect()->back()->with('success', 'Verification request cancelled.');
    }
}
