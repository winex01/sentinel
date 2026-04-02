<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            License
        </x-slot>

        <x-slot name="description">
            Your current license information.
        </x-slot>

        <div class="flex items-center justify-between">
            <div class="space-y-1 text-sm text-gray-500 dark:text-gray-400">
                <div class="-mt-3">App ID: <span class="font-mono">{{ $this->getAppId() }}</span></div>
                @if(\Winex\Sentinel\SentinelService::isOnTrial(auth()->user()))
                    <div>
                        @php
                            $trialDaysRemaining = \Winex\Sentinel\SentinelService::trialDaysRemaining(auth()->user());
                        @endphp
                        Trial Ends In:
                        <span class="font-medium {{ \Winex\Sentinel\SentinelService::trialCssColor($trialDaysRemaining) }}">
                            {{ trans_choice(':count day remaining|:count days remaining', $trialDaysRemaining) }}
                        </span>
                    </div>
                @else
                    <div>Active Until: <span class="font-medium {{ $this->getExpiresAtColor() }}">{{ $this->getExpiresAt() }}</span></div>
                @endif
            </div>

            <x-filament::button
                tag="a"
                href="{{ route('filament.app.tenant.billing') }}"
                color="gray"
                icon="heroicon-o-key"
            >
                License
            </x-filament::button>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
