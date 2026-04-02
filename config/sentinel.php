<?php
return [
    'public_key_path' => env('SENTINEL_PUBLIC_KEY_PATH', base_path('public.pem')),
    'trial_days' => env('SENTINEL_TRIAL_DAYS', 30),
];