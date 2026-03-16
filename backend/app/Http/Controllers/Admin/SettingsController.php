<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function edit(): View
    {
        $settings = Setting::getInstance();

        return view('admin.settings.edit', compact('settings'));
    }

    public function update(Request $request): RedirectResponse
    {
        $settings = Setting::getInstance();

        $validated = $request->validate([
            'site_name' => 'required|string|max:255',
            'site_description' => 'nullable|string|max:500',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'favicon' => 'nullable|image|mimes:ico,png|max:2048',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:50',
            'contact_address' => 'nullable|string|max:500',
            'facebook_url' => 'nullable|url|max:255',
            'twitter_url' => 'nullable|url|max:255',
            'instagram_url' => 'nullable|url|max:255',
            'linkedin_url' => 'nullable|url|max:255',
            'youtube_url' => 'nullable|url|max:255',
            'whatsapp_number' => 'nullable|string|max:50',
            'footer_text' => 'nullable|string|max:1000',
            'terms_content' => 'nullable|string',
            'privacy_content' => 'nullable|string',
            'about_content' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
            'currency_symbol' => 'required|string|max:10',
            'currency_code' => 'required|string|max:10',
        ]);

        // Handle file uploads
        if ($request->hasFile('logo')) {
            // Delete old logo
            if ($settings->logo) {
                Storage::disk('public')->delete($settings->logo);
            }
            $validated['logo'] = $request->file('logo')->store('settings', 'public');
        }

        if ($request->hasFile('favicon')) {
            // Delete old favicon
            if ($settings->favicon) {
                Storage::disk('public')->delete($settings->favicon);
            }
            $validated['favicon'] = $request->file('favicon')->store('settings', 'public');
        }

        // Handle meta keywords (convert comma-separated to array)
        if (!empty($validated['meta_keywords'])) {
            $validated['meta_keywords'] = array_map('trim', explode(',', $validated['meta_keywords']));
        }

        $settings->update($validated);

        return back()->with('success', 'Settings updated successfully.');
    }

    public function removeLogo(): RedirectResponse
    {
        $settings = Setting::getInstance();

        if ($settings->logo) {
            Storage::disk('public')->delete($settings->logo);
            $settings->update(['logo' => null]);
        }

        return back()->with('success', 'Logo removed successfully.');
    }

    public function removeFavicon(): RedirectResponse
    {
        $settings = Setting::getInstance();

        if ($settings->favicon) {
            Storage::disk('public')->delete($settings->favicon);
            $settings->update(['favicon' => null]);
        }

        return back()->with('success', 'Favicon removed successfully.');
    }
}
