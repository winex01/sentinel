<?php

namespace Winex\Sentinel;

use Winex\Sentinel\Filament\Pages\SentinelPage;
use Winex\Sentinel\Http\Middleware\SentinelMiddleware;
use Filament\Billing\Providers\Contracts\BillingProvider;

class SentinelProvider implements BillingProvider
{
    public function getRouteAction(): string
    {
        return SentinelPage::class;
    }

    public function getSubscribedMiddleware(): string
    {
        return SentinelMiddleware::class;
    }
}