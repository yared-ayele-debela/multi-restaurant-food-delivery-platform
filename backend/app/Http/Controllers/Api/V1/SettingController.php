<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;

class SettingController extends Controller
{
    public function show(): JsonResponse
    {
        $settings = Setting::getInstance();

        return response()->json([
            'data' => [
                'site_name' => $settings->site_name,
                'site_description' => $settings->site_description,
                'logo' => $settings->logo,
                'logo_url' => $settings->getLogoUrl(),
                'favicon' => $settings->favicon,
                'favicon_url' => $settings->getFaviconUrl(),
                'contact_email' => $settings->contact_email,
                'contact_phone' => $settings->contact_phone,
                'contact_address' => $settings->contact_address,
                'facebook_url' => $settings->facebook_url,
                'twitter_url' => $settings->twitter_url,
                'instagram_url' => $settings->instagram_url,
                'linkedin_url' => $settings->linkedin_url,
                'youtube_url' => $settings->youtube_url,
                'whatsapp_number' => $settings->whatsapp_number,
                'footer_text' => $settings->footer_text,
                'currency_symbol' => $settings->currency_symbol,
                'currency_code' => $settings->currency_code,
                'meta_keywords' => $settings->meta_keywords ?: [],
            ],
        ]);
    }
}
