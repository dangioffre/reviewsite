<?php

namespace App\Providers;

use SocialiteProviders\Manager\SocialiteWasCalled;

class KickSocialiteProvider
{
    public function handle(SocialiteWasCalled $socialiteWasCalled)
    {
        $socialiteWasCalled->extendSocialite('kick', \App\Services\KickOAuthProvider::class);
    }
} 