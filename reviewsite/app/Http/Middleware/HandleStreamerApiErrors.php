<?php

namespace App\Http\Middleware;

use App\Exceptions\OAuthException;
use App\Exceptions\PlatformApiException;
use App\Exceptions\StreamerProfileException;
use App\Services\StreamerErrorHandlingService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class HandleStreamerApiErrors
{
    private StreamerErrorHandlingService $errorHandler;

    public function __construct(StreamerErrorHandlingService $errorHandler)
    {
        $this->errorHandler = $errorHandler;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            return $next($request);
        } catch (StreamerProfileException $e) {
            return $this->handleStreamerException($request, $e);
        } catch (\Exception $e) {
            // Let other exceptions bubble up normally
            throw $e;
        }
    }

    /**
     * Handle streamer-specific exceptions
     *
     * @param Request $request
     * @param StreamerProfileException $exception
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    private function handleStreamerException(Request $request, StreamerProfileException $exception)
    {
        $userMessage = $this->errorHandler->getUserFriendlyMessage($exception);
        
        // Log the error with context
        Log::error('Streamer API error handled by middleware', [
            'exception' => get_class($exception),
            'message' => $exception->getMessage(),
            'code' => $exception->getCode(),
            'context' => $exception->getContext(),
            'url' => $request->fullUrl(),
            'user_id' => $request->user()?->id,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        // Handle different request types
        if ($request->expectsJson()) {
            return $this->handleJsonResponse($exception, $userMessage);
        }

        return $this->handleWebResponse($request, $exception, $userMessage);
    }

    /**
     * Handle JSON API responses
     *
     * @param StreamerProfileException $exception
     * @param string $userMessage
     * @return \Illuminate\Http\JsonResponse
     */
    private function handleJsonResponse(StreamerProfileException $exception, string $userMessage)
    {
        $statusCode = $this->getHttpStatusCode($exception);
        
        $response = [
            'error' => true,
            'message' => $userMessage,
            'type' => $this->getErrorType($exception)
        ];

        // Add retry information for rate limiting
        if ($exception instanceof PlatformApiException && 
            $exception->getCode() === PlatformApiException::RATE_LIMITED) {
            $context = $exception->getContext();
            if (isset($context['retry_after'])) {
                $response['retry_after'] = $context['retry_after'];
            }
        }

        // Add fallback data if available
        if ($this->errorHandler->shouldUseFallback($exception)) {
            $response['fallback_available'] = true;
        }

        return response()->json($response, $statusCode);
    }

    /**
     * Handle web responses
     *
     * @param Request $request
     * @param StreamerProfileException $exception
     * @param string $userMessage
     * @return \Illuminate\Http\RedirectResponse
     */
    private function handleWebResponse(Request $request, StreamerProfileException $exception, string $userMessage)
    {
        $redirectRoute = $this->getRedirectRoute($request, $exception);
        $flashType = $this->getFlashType($exception);

        $redirect = redirect()->route($redirectRoute);

        // Add appropriate flash message
        if ($flashType === 'warning' && $this->errorHandler->shouldUseFallback($exception)) {
            $redirect->with('warning', $userMessage . ' Some information may be outdated.');
        } else {
            $redirect->with($flashType, $userMessage);
        }

        // Add retry information for rate limiting
        if ($exception instanceof PlatformApiException && 
            $exception->getCode() === PlatformApiException::RATE_LIMITED) {
            $context = $exception->getContext();
            if (isset($context['retry_after'])) {
                $redirect->with('retry_after', $context['retry_after']);
            }
        }

        return $redirect;
    }

    /**
     * Get appropriate HTTP status code for exception
     *
     * @param StreamerProfileException $exception
     * @return int
     */
    private function getHttpStatusCode(StreamerProfileException $exception): int
    {
        if ($exception instanceof OAuthException) {
            return match ($exception->getCode()) {
                OAuthException::INVALID_STATE => 400,
                OAuthException::TOKEN_EXPIRED => 401,
                OAuthException::ACCOUNT_ALREADY_CONNECTED => 409,
                OAuthException::UNSUPPORTED_PLATFORM => 400,
                default => 400
            };
        }

        if ($exception instanceof PlatformApiException) {
            return match ($exception->getCode()) {
                PlatformApiException::RATE_LIMITED => 429,
                PlatformApiException::API_UNAVAILABLE => 503,
                PlatformApiException::AUTHENTICATION_FAILED => 401,
                PlatformApiException::RESOURCE_NOT_FOUND => 404,
                PlatformApiException::QUOTA_EXCEEDED => 429,
                PlatformApiException::NETWORK_ERROR => 502,
                default => 500
            };
        }

        return 500;
    }

    /**
     * Get error type for API responses
     *
     * @param StreamerProfileException $exception
     * @return string
     */
    private function getErrorType(StreamerProfileException $exception): string
    {
        if ($exception instanceof OAuthException) {
            return 'oauth_error';
        }

        if ($exception instanceof PlatformApiException) {
            return 'platform_api_error';
        }

        return 'streamer_error';
    }

    /**
     * Get appropriate redirect route based on request and exception
     *
     * @param Request $request
     * @param StreamerProfileException $exception
     * @return string
     */
    private function getRedirectRoute(Request $request, StreamerProfileException $exception): string
    {
        // OAuth errors should redirect to dashboard
        if ($exception instanceof OAuthException) {
            return 'dashboard';
        }

        // Try to redirect back to a sensible page based on current route
        $currentRoute = $request->route()?->getName();
        
        if (str_starts_with($currentRoute, 'streamer.profile.')) {
            return 'streamer.profiles.index';
        }

        if (str_starts_with($currentRoute, 'streamer.')) {
            return 'dashboard';
        }

        // Default fallback
        return 'dashboard';
    }

    /**
     * Get appropriate flash message type
     *
     * @param StreamerProfileException $exception
     * @return string
     */
    private function getFlashType(StreamerProfileException $exception): string
    {
        if ($exception instanceof PlatformApiException) {
            return match ($exception->getCode()) {
                PlatformApiException::RATE_LIMITED,
                PlatformApiException::QUOTA_EXCEEDED => 'warning',
                PlatformApiException::API_UNAVAILABLE,
                PlatformApiException::NETWORK_ERROR => 'warning',
                default => 'error'
            };
        }

        return 'error';
    }
}