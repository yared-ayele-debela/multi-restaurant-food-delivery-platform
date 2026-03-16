<?php

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

function isAdmin()
{
    return auth()->check() && auth()->user()->hasRole('admin|super-admin');
}

/**
 * Get setting value by key
 *
 * @param string $key
 * @param mixed $default
 * @return mixed
 */
function setting(string $key, mixed $default = null): mixed
{
    $settings = Cache::remember('site_settings_array', 3600, function () {
        $setting = Setting::first();
        if (!$setting) {
            return [];
        }
        return $setting->toArray();
    });

    return $settings[$key] ?? $default;
}

/**
 * Get site name
 *
 * @return string
 */
function site_name(): string
{
    return setting('site_name', 'Food Delivery');
}

/**
 * Get logo URL
 *
 * @return string|null
 */
function logo_url(): ?string
{
    $path = setting('logo');
    if (empty($path)) {
        return null;
    }
    return asset('storage/' . $path);
}

/**
 * Get favicon URL
 *
 * @return string|null
 */
function favicon_url(): ?string
{
    $path = setting('favicon');
    if (empty($path)) {
        return null;
    }
    return asset('storage/' . $path);
}

