<?php

namespace Tests\Unit\Middleware;

use App\Http\Middleware\HandleStreamerApiErrors;
use App\Exceptions\OAuthException;
use App\Exceptions\PlatformApiException;
use App\Services\StreamerErrorHandlingService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Tests\TestCase;
use Mockery;

class HandleStreamerApiErrorsTest extends TestCase
{
    private HandleStreamerApiErrors $middleware;
    private StreamerErrorHandlingService $errorHandler;

    protected function setUp(): void
    {
        parent::setUp();
        $this->errorHandler = Mockery::mock(StreamerErrorHandlingService::class);
        $this->middleware = new HandleStreamerApiErrors($this->errorHandler);
    }

    public function test_passes_through_successful_requests()
    {
        $request = Request::create('/test');
        $response = new Response('Success');

        $next = function ($req) use ($response) {
            return $response;
        };

        $result = $this->middleware->handle($request, $next);

        $this->assertSame($response, $result);
    }

    public function test_handles_oauth_exception_with_json_request()
    {
        $request = Request::create('/api/test');
        $request->headers->set('Accept', 'application/json');
        
        $exception = OAuthException::invalidState('twitch');
        $userMessage = 'Connection security check failed';

        $this->errorHandler
            ->shouldReceive('getUserFriendlyMessage')
            ->with($exception)
            ->andReturn($userMessage);

        $this->errorHandler
            ->shouldReceive('shouldUseFallback')
            ->with($exception)
            ->andReturn(false);

        $next = function ($req) use ($exception) {
            throw $exception;
        };

        $result = $this->middleware->handle($request, $next);

        $this->assertEquals(400, $result->getStatusCode());
        $data = json_decode($result->getContent(), true);
        $this->assertEquals($userMessage, $data['message']);
        $this->assertEquals('oauth_error', $data['type']);
        $this->assertTrue($data['error']);
    }

    public function test_handles_platform_api_exception_with_rate_limiting()
    {
        $request = Request::create('/api/test');
        $request->headers->set('Accept', 'application/json');
        
        $exception = PlatformApiException::rateLimited('youtube', 300);
        $userMessage = 'Rate limit exceeded';

        $this->errorHandler
            ->shouldReceive('getUserFriendlyMessage')
            ->with($exception)
            ->andReturn($userMessage);

        $this->errorHandler
            ->shouldReceive('shouldUseFallback')
            ->with($exception)
            ->andReturn(false);

        $next = function ($req) use ($exception) {
            throw $exception;
        };

        $result = $this->middleware->handle($request, $next);

        $this->assertEquals(429, $result->getStatusCode());
        $data = json_decode($result->getContent(), true);
        $this->assertEquals($userMessage, $data['message']);
        $this->assertEquals('platform_api_error', $data['type']);
        $this->assertEquals(300, $data['retry_after']);
    }

    public function test_handles_platform_api_exception_with_fallback_available()
    {
        $request = Request::create('/api/test');
        $request->headers->set('Accept', 'application/json');
        
        $exception = PlatformApiException::apiUnavailable('twitch');
        $userMessage = 'API unavailable';

        $this->errorHandler
            ->shouldReceive('getUserFriendlyMessage')
            ->with($exception)
            ->andReturn($userMessage);

        $this->errorHandler
            ->shouldReceive('shouldUseFallback')
            ->with($exception)
            ->andReturn(true);

        $next = function ($req) use ($exception) {
            throw $exception;
        };

        $result = $this->middleware->handle($request, $next);

        $this->assertEquals(503, $result->getStatusCode());
        $data = json_decode($result->getContent(), true);
        $this->assertTrue($data['fallback_available']);
    }

    public function test_handles_oauth_exception_with_web_request()
    {
        $request = Request::create('/streamer/oauth/callback/twitch');
        $request->setRouteResolver(function () {
            $route = Mockery::mock();
            $route->shouldReceive('getName')->andReturn('streamer.oauth.callback');
            return $route;
        });
        
        $exception = OAuthException::accountAlreadyConnected('twitch');
        $userMessage = 'Account already connected';

        $this->errorHandler
            ->shouldReceive('getUserFriendlyMessage')
            ->with($exception)
            ->andReturn($userMessage);

        $next = function ($req) use ($exception) {
            throw $exception;
        };

        $result = $this->middleware->handle($request, $next);

        $this->assertEquals(302, $result->getStatusCode());
        $this->assertStringContainsString('dashboard', $result->getTargetUrl());
    }

    public function test_handles_platform_api_exception_with_warning_flash()
    {
        $request = Request::create('/streamer/profile/1');
        $request->setRouteResolver(function () {
            $route = Mockery::mock();
            $route->shouldReceive('getName')->andReturn('streamer.profile.show');
            return $route;
        });
        
        $exception = PlatformApiException::rateLimited('kick', 120);
        $userMessage = 'Rate limit exceeded';

        $this->errorHandler
            ->shouldReceive('getUserFriendlyMessage')
            ->with($exception)
            ->andReturn($userMessage);

        $this->errorHandler
            ->shouldReceive('shouldUseFallback')
            ->with($exception)
            ->andReturn(true);

        $next = function ($req) use ($exception) {
            throw $exception;
        };

        $result = $this->middleware->handle($request, $next);

        $this->assertEquals(302, $result->getStatusCode());
        // The middleware redirects to 'streamer.profiles.index' route which becomes '/streamers'
        $this->assertStringContainsString('streamers', $result->getTargetUrl());
    }

    public function test_lets_non_streamer_exceptions_bubble_up()
    {
        $request = Request::create('/test');
        $exception = new \InvalidArgumentException('Not a streamer exception');

        $next = function ($req) use ($exception) {
            throw $exception;
        };

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Not a streamer exception');

        $this->middleware->handle($request, $next);
    }

    public function test_determines_correct_http_status_codes()
    {
        $testCases = [
            [OAuthException::invalidState('twitch'), 400],
            [OAuthException::tokenExpired('youtube'), 401],
            [OAuthException::accountAlreadyConnected('kick'), 409],
            [PlatformApiException::rateLimited('twitch'), 429],
            [PlatformApiException::apiUnavailable('youtube'), 503],
            [PlatformApiException::authenticationFailed('kick'), 401],
            [PlatformApiException::resourceNotFound('twitch', 'video'), 404],
            [PlatformApiException::quotaExceeded('youtube'), 429],
            [PlatformApiException::networkError('kick', 'timeout'), 502],
        ];

        foreach ($testCases as [$exception, $expectedStatus]) {
            $request = Request::create('/api/test');
            $request->headers->set('Accept', 'application/json');

            $this->errorHandler
                ->shouldReceive('getUserFriendlyMessage')
                ->with($exception)
                ->andReturn('Test message');

            $this->errorHandler
                ->shouldReceive('shouldUseFallback')
                ->with($exception)
                ->andReturn(false);

            $next = function ($req) use ($exception) {
                throw $exception;
            };

            $result = $this->middleware->handle($request, $next);
            $this->assertEquals($expectedStatus, $result->getStatusCode(), 
                "Failed for exception: " . get_class($exception) . " with code: " . $exception->getCode());
        }
    }

    public function test_determines_correct_redirect_routes()
    {
        $testCases = [
            ['streamer.oauth.callback', OAuthException::invalidState('twitch'), 'dashboard'],
            ['streamer.profile.show', PlatformApiException::apiUnavailable('youtube'), 'streamers'],
            ['streamer.profile.edit', PlatformApiException::rateLimited('kick'), 'streamers'],
            ['dashboard', PlatformApiException::networkError('twitch', 'timeout'), 'dashboard'],
        ];

        foreach ($testCases as [$routeName, $exception, $expectedRedirectPath]) {
            $request = Request::create('/test');
            $request->setRouteResolver(function () use ($routeName) {
                $route = Mockery::mock();
                $route->shouldReceive('getName')->andReturn($routeName);
                return $route;
            });

            $this->errorHandler
                ->shouldReceive('getUserFriendlyMessage')
                ->with($exception)
                ->andReturn('Test message');

            $this->errorHandler
                ->shouldReceive('shouldUseFallback')
                ->with($exception)
                ->andReturn(false);

            $next = function ($req) use ($exception) {
                throw $exception;
            };

            $result = $this->middleware->handle($request, $next);
            $this->assertStringContainsString($expectedRedirectPath, $result->getTargetUrl(),
                "Failed for route: {$routeName} with exception: " . get_class($exception));
        }
    }
}