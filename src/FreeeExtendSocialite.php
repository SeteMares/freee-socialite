<?php

namespace SeteMares\Freee;

use SocialiteProviders\Manager\SocialiteWasCalled;

class FreeeExtendSocialite
{
    /**
     * Register the provider.
     *
     * @param \SocialiteProviders\Manager\SocialiteWasCalled $socialiteWasCalled
     */
    public function handle(SocialiteWasCalled $socialiteWasCalled)
    {
        $socialiteWasCalled->extendSocialite(
            'freee',
            __NAMESPACE__ . '\Provider'
        );
    }
}
