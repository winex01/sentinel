# Sentinel - Laravel License & Billing Management

Sentinel is a Laravel package that provides license management, trial periods, and billing integration for FilamentPHP applications.

## Requirements

- PHP ^8.2
- Laravel ^12.0
- Filament ^5.0

## Installation
```bash
composer require winex/sentinel
```

## Setup

1. Install the package
```bash
php artisan sentinel:install
php artisan migrate
```

2. Add to Filament Panel Provider
```php
use Winex\Sentinel\SentinelProvider;
use Winex\Sentinel\Filament\Pages\SentinelPage;

public function panel(Panel $panel): Panel
{
    return $panel
        ->requiresTenantSubscription()
        ->tenantBillingProvider(new SentinelProvider())
        ->pages([
            SentinelPage::class,
        ]);
}
```

## Configuration

Add these to your `.env` file (optional):
```env
MONTHLY_PLAN=30
ANNUAL_PLAN=350
CONTACT_US=https://example.com/contact
```

## Commands

| Command | Description |
|---|---|
| `php artisan sentinel:install` | Publish migration with current timestamp |
