<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class Setting extends Model
{
    /** @use HasFactory<\Database\Factories\SettingFactory> */
    use HasFactory;

    protected $fillable = [
        'site_name',
        'site_description',
        'logo',
        'favicon',
        'contact_email',
        'contact_phone',
        'contact_address',
        'facebook_url',
        'twitter_url',
        'instagram_url',
        'linkedin_url',
        'youtube_url',
        'whatsapp_number',
        'footer_text',
        'terms_content',
        'privacy_content',
        'about_content',
        'meta_keywords',
        'currency_symbol',
        'currency_code',
    ];

    protected function casts(): array
    {
        return [
            'meta_keywords' => 'array',
        ];
    }

    /**
     * Get the single settings record (singleton pattern)
     */
    public static function getInstance(): self
    {
        return Cache::remember('site_settings', 3600, function () {
            return self::firstOrCreate([], [
                'site_name' => 'Food Delivery',
                'currency_symbol' => '$',
                'currency_code' => 'USD',
            ]);
        });
    }

    /**
     * Clear the settings cache
     */
    public static function clearCache(): void
    {
        Cache::forget('site_settings');
    }

    protected static function boot(): void
    {
        parent::boot();

        static::saved(function () {
            self::clearCache();
        });

        static::deleted(function () {
            self::clearCache();
        });
    }

    /**
     * Get logo URL
     */
    public function getLogoUrl(): ?string
    {
        if (empty($this->logo)) {
            return null;
        }
        return asset('storage/' . $this->logo);
    }

    /**
     * Get favicon URL
     */
    public function getFaviconUrl(): ?string
    {
        if (empty($this->favicon)) {
            return null;
        }
        return asset('storage/' . $this->favicon);
    }
}
