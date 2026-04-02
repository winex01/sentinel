<?php

namespace Winex\Sentinel\Filament\Widgets;

use Filament\Widgets\Widget;
use Filament\Facades\Filament;
use Winex\Sentinel\Models\Sentinel;
use Winex\Sentinel\SentinelService;

class SentinelWidget extends Widget
{
    protected string $view = 'sentinel::widgets.sentinel-widget';
    protected int|string|array $columnSpan = 'half';

    protected $license;

    public static function canView(): bool
    {
        return Filament::auth()->check();
    }

    public function mount(): void
    {
        $this->license = Sentinel::where('user_id', auth()->id())
            ->orderBy('expires_at', 'desc')
            ->first();
    }

    public function getAppId(): string
    {
        return SentinelService::getAppId(auth()->user());
    }

    public function getExpiresAt(): string
    {
        if (!$this->license) {
            return 'No active license';
        }

        return $this->license->expires_at->format('M d, Y');
    }

    public function getExpiresAtColor(): string
    {
        if (!$this->license) {
            return 'text-gray-500';
        }

        $days = now()->diffInDays($this->license->expires_at, false);

        return SentinelService::trialCssColor($days);
    }
}
