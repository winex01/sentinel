<?php

namespace Winex\Sentinel;

use Winex\Sentinel\Models\Sentinel;
use Illuminate\Contracts\Auth\Authenticatable;

class SentinelService
{   
    public static function getPublicKey()
    {
        return cache()->rememberForever('sentinel_public_key', function () {
            $keyPath = config('sentinel.public_key_path', base_path('public.pem'));
            return file_get_contents($keyPath);
        });
    }

    public static function clearPublicKeyCache()
    {
        return cache()->forget('sentinel_public_key');
    }

    public static function getTrialDays(): int
    {
        return config('sentinel.trial_days', 30);
    }

    public static function getMachineId(): string
    {
        return cache()->rememberForever('machine_id', function () {
            $motherboard = shell_exec('wmic baseboard get serialnumber 2>&1');
            $cpu = shell_exec('wmic cpu get processorid 2>&1');
            $mac = shell_exec('wmic nic where "NetEnabled=true" get MACAddress 2>&1');

            return md5($motherboard . $cpu . $mac);
        });
    }

    public static function getAppId(Authenticatable $user): string
    {
        $raw = $user->id . $user->created_at . config('app.key');
        $hash = strtoupper(md5($raw));
        return implode('-', str_split($hash, 4));
    }

    public static function verifyLicenseFile(string $path, Authenticatable $user): array
    {
        $fullPath = str_starts_with($path, storage_path())
            ? $path
            : storage_path('app/private/' . $path);

        if (!file_exists($fullPath)) {
            return ['valid' => false, 'message' => 'License file not found.'];
        }

        $license = json_decode(file_get_contents($fullPath), true);

        if (!$license) {
            return ['valid' => false, 'message' => 'Invalid license file.'];
        }

        $data = json_encode([
            'app_id' => $license['app_id'],
            'expires_at' => $license['expires_at'],
        ]);

        try {
            $verified = openssl_verify($data, base64_decode($license['signature']), static::getPublicKey(), OPENSSL_ALGO_SHA256);
        } catch (\Throwable $th) {
            // $encoded = "DQogICAg4pWU4pWQ4pWQ4pWQ4pWQ4pWQ4pWQ4pWQ4pWQ4pWQ4pWQ4pWQ4pWQ4pWQ4pWQ4pWQ4pWQ4pWQ4pWQ4pWQ4pWQ4pWQ4pWQ4pWQ4pWQ4pWQ4pWQ4pWQ4pWQ4pWQ4pWQ4pWQ4pWQ4pWQ4pWQ4pWQ4pWQ4pWQ4pWQ4pWQ4pWQ4pWQ4pWQ4pWQ4pWQ4pWQ4pWQ4pWQ4pWQ4pWQ4pWQ4pWQ4pWQ4pWQ4pWQ4pWQ4pWQ4pWQ4pWQ4pWXDQogICAg4pWRICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIOKVkQ0KICAgIOKVkSAgICAg8J+WlSAgWU9VIFJFQUxMWSBUSE9VR0hUIFlPVSBDT1VMRCBDUkFDSyBJVD8gIPCflpUgICAgICDilZENCiAgICDilZEgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAg4pWRDQogICAg4pWRICAgICBOaWNlIHRyeSwgYnV0IHRoZSBsaWNlbnNlIHN5c3RlbSBzYXlzOiAgICAgICAgICAgICAgIOKVkQ0KICAgIOKVkSAgICAgJ0dPT0QgTFVDSyBXSVRIIFlPVVIgQ1JBQ0tFRCBWRVJTSU9OLCBDSEFNUCEnICAgICAgICDilZENCiAgICDilZEgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAg4pWRDQogICAg4pWRICAgICBIb3BlIHlvdSBlbmpveSB0aGUgYnVncyEg8J+koSDwn6ShICAgICAgICAgICAgICAgICAgICAgIOKVkQ0KICAgIOKVkSAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICDilZENCiAgICDilZrilZDilZDilZDilZDilZDilZDilZDilZDilZDilZDilZDilZDilZDilZDilZDilZDilZDilZDilZDilZDilZDilZDilZDilZDilZDilZDilZDilZDilZDilZDilZDilZDilZDilZDilZDilZDilZDilZDilZDilZDilZDilZDilZDilZDilZDilZDilZDilZDilZDilZDilZDilZDilZDilZDilZDilZDilZDilZDilZ0NCiAgICA=";
            // $msg = base64_decode($encoded);
            // echo "<pre>";
            // echo $msg;
            // echo "</pre>";
            // exit;
            $verified = null;
        }

        if ($verified !== 1) {
            return ['valid' => false, 'message' => 'License signature is invalid.'];
        }

        if ($license['app_id'] !== static::getAppId($user)) {
            return ['valid' => false, 'message' => 'License does not match this machine.'];
        }

        if (now()->gt($license['expires_at'])) {
            return ['valid' => false, 'message' => 'License has expired.'];
        }

        return [
            'valid' => true,
            'app_id' => $license['app_id'],
            'signature' => $license['signature'],
            'expires_at' => $license['expires_at'],
            'message' => 'License is valid.',
        ];
    }

    public static function isSubscribed(Authenticatable $user): bool
    {
        $license = Sentinel::where('user_id', $user->id)
            ->orderBy('expires_at', 'desc')
            ->first();

        if (!$license) {
            return false;
        }

        $result = static::verifyLicenseFile($license->file_path, $user);

        return $result['valid'];
    }

    public static function isOnTrial(Authenticatable $user): bool
    {
        return $user->created_at->diffInDays(now()) <= static::getTrialDays();
    }

    public static function trialDaysRemaining(Authenticatable $user): int
    {
        return max(0, static::getTrialDays() - (int) $user->created_at->diffInDays(now()));
    }

    public static function trialCssColor(int $days): string
    {
        if ($days < 0) return 'text-gray-500';
        if ($days < 2) return 'text-warning-500';
        if ($days <= 29) return 'text-info-500';

        return 'text-primary-500';
    }
}