<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Public Key Path for License Verification
    |--------------------------------------------------------------------------
    |
    | Path to the public key file. If the file doesn't exist, the default
    | public key below will be used.
    |
    */
    'public_key_path' => env('SENTINEL_PUBLIC_KEY_PATH', storage_path('app/private/public.key')),

    /*
    |--------------------------------------------------------------------------
    | Default Public Key
    |--------------------------------------------------------------------------
    |
    | This is the fallback public key if the key file doesn't exist.
    |
    */
    'default_public_key' => "-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA6xY7RClrw7dsGBzx0VIq
PVK4S3ldblCb5mo3t1pqqp0oIMu4TTYI2gohMH7VzxbDg6ZWTJgjl+C6f4zVjUmb
7GiBHsrfqaTMSx68HXWWdZ2JxZAzkbIpPuTmMbXBqvRyr5CQ+vUg8lvosfnG+lP1
2hgp5vxYm2WDsrPyAtTUBeqwOub8FL6vCrkG6HYON95M+CAF1UpqJZomNc5qZnng
dJd784g0+VMSnIQKKXWUctGIIVEAn1rHVswJRq+2poLyoyQL+DUM4vjby55s6/i
DOc4FwTX89aV1xGIprZXmNJrrisMh6KPaxTdDcPhpppkymPZwLkcKjtcQGrB/9eY
7wIDAQAB
-----END PUBLIC KEY-----",

    'trial_days' => env('SENTINEL_TRIAL_DAYS', 30),
];