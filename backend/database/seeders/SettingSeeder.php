<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        Setting::firstOrCreate(
            ['id' => 1],
            [
                'site_name' => 'Food Delivery',
                'site_description' => 'Your favorite food delivery platform',
                'logo' => null,
                'favicon' => null,
                'contact_email' => 'support@fooddelivery.com',
                'contact_phone' => '+1 (555) 123-4567',
                'contact_address' => '123 Main Street, New York, NY 10001',
                'facebook_url' => null,
                'twitter_url' => null,
                'instagram_url' => null,
                'linkedin_url' => null,
                'youtube_url' => null,
                'whatsapp_number' => null,
                'footer_text' => '© ' . date('Y') . ' Food Delivery. All rights reserved.',
                'terms_content' => null,
                'privacy_content' => null,
                'about_content' => null,
                'meta_keywords' => ['food', 'delivery', 'restaurant', 'online ordering'],
                'currency_symbol' => '$',
                'currency_code' => 'USD',
            ]
        );
    }
}
