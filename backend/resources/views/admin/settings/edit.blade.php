@extends('admin.layouts.app')

@section('title')
    Website Settings
@endsection

@section('content')
<div class="container-fluid">
    <x-page-title
        title="Website Settings"
        :breadcrumbs="[
            ['label' => 'Admin', 'url' => route('admin.dashboard')],
            ['label' => 'Settings'],
        ]"
    />

    <x-alert />

    <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row">
            <!-- General Settings -->
            <div class="col-xl-6">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">General Information</h4>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="site_name" class="form-label">Site Name <span class="text-danger">*</span></label>
                            <input type="text" id="site_name" name="site_name" class="form-control @error('site_name') is-invalid @enderror"
                                   value="{{ old('site_name', $settings->site_name) }}" required>
                            @error('site_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="site_description" class="form-label">Site Description</label>
                            <textarea id="site_description" name="site_description" rows="3" class="form-control @error('site_description') is-invalid @enderror">{{ old('site_description', $settings->site_description) }}</textarea>
                            @error('site_description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="currency_symbol" class="form-label">Currency Symbol <span class="text-danger">*</span></label>
                            <input type="text" id="currency_symbol" name="currency_symbol" class="form-control @error('currency_symbol') is-invalid @enderror"
                                   value="{{ old('currency_symbol', $settings->currency_symbol) }}" required>
                            @error('currency_symbol')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="currency_code" class="form-label">Currency Code <span class="text-danger">*</span></label>
                            <input type="text" id="currency_code" name="currency_code" class="form-control @error('currency_code') is-invalid @enderror"
                                   value="{{ old('currency_code', $settings->currency_code) }}" required>
                            @error('currency_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="footer_text" class="form-label">Footer Text</label>
                            <textarea id="footer_text" name="footer_text" rows="2" class="form-control @error('footer_text') is-invalid @enderror">{{ old('footer_text', $settings->footer_text) }}</textarea>
                            @error('footer_text')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Branding -->
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Branding</h4>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="logo" class="form-label">Logo</label>
                            @if($settings->logo)
                                <div class="mb-2">
                                    <img src="{{ $settings->getLogoUrl() }}" alt="Current Logo" class="img-thumbnail" style="max-height: 80px;">
                                    <a href="{{ route('admin.settings.remove-logo') }}" class="btn btn-sm btn-danger ms-2" onclick="return confirm('Remove logo?');">
                                        <i class="mdi mdi-delete"></i> Remove
                                    </a>
                                </div>
                            @endif
                            <input type="file" id="logo" name="logo" class="form-control @error('logo') is-invalid @enderror" accept="image/*">
                            <small class="text-muted">Recommended: 200x60px, PNG or JPG</small>
                            @error('logo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-0">
                            <label for="favicon" class="form-label">Favicon</label>
                            @if($settings->favicon)
                                <div class="mb-2">
                                    <img src="{{ $settings->getFaviconUrl() }}" alt="Current Favicon" class="img-thumbnail" style="max-height: 32px;">
                                    <a href="{{ route('admin.settings.remove-favicon') }}" class="btn btn-sm btn-danger ms-2" onclick="return confirm('Remove favicon?');">
                                        <i class="mdi mdi-delete"></i> Remove
                                    </a>
                                </div>
                            @endif
                            <input type="file" id="favicon" name="favicon" class="form-control @error('favicon') is-invalid @enderror" accept=".ico,.png">
                            <small class="text-muted">Recommended: 32x32px, ICO or PNG</small>
                            @error('favicon')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact & Social -->
            <div class="col-xl-6">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Contact Information</h4>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="contact_email" class="form-label">Contact Email</label>
                            <input type="email" id="contact_email" name="contact_email" class="form-control @error('contact_email') is-invalid @enderror"
                                   value="{{ old('contact_email', $settings->contact_email) }}">
                            @error('contact_email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="contact_phone" class="form-label">Contact Phone</label>
                            <input type="text" id="contact_phone" name="contact_phone" class="form-control @error('contact_phone') is-invalid @enderror"
                                   value="{{ old('contact_phone', $settings->contact_phone) }}">
                            @error('contact_phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="contact_address" class="form-label">Contact Address</label>
                            <textarea id="contact_address" name="contact_address" rows="3" class="form-control @error('contact_address') is-invalid @enderror">{{ old('contact_address', $settings->contact_address) }}</textarea>
                            @error('contact_address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Social Media Links</h4>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="facebook_url" class="form-label"><i class="mdi mdi-facebook me-1"></i> Facebook URL</label>
                            <input type="url" id="facebook_url" name="facebook_url" class="form-control @error('facebook_url') is-invalid @enderror"
                                   value="{{ old('facebook_url', $settings->facebook_url) }}" placeholder="https://facebook.com/...">
                            @error('facebook_url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="twitter_url" class="form-label"><i class="mdi mdi-twitter me-1"></i> Twitter URL</label>
                            <input type="url" id="twitter_url" name="twitter_url" class="form-control @error('twitter_url') is-invalid @enderror"
                                   value="{{ old('twitter_url', $settings->twitter_url) }}" placeholder="https://twitter.com/...">
                            @error('twitter_url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="instagram_url" class="form-label"><i class="mdi mdi-instagram me-1"></i> Instagram URL</label>
                            <input type="url" id="instagram_url" name="instagram_url" class="form-control @error('instagram_url') is-invalid @enderror"
                                   value="{{ old('instagram_url', $settings->instagram_url) }}" placeholder="https://instagram.com/...">
                            @error('instagram_url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="linkedin_url" class="form-label"><i class="mdi mdi-linkedin me-1"></i> LinkedIn URL</label>
                            <input type="url" id="linkedin_url" name="linkedin_url" class="form-control @error('linkedin_url') is-invalid @enderror"
                                   value="{{ old('linkedin_url', $settings->linkedin_url) }}" placeholder="https://linkedin.com/...">
                            @error('linkedin_url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="youtube_url" class="form-label"><i class="mdi mdi-youtube me-1"></i> YouTube URL</label>
                            <input type="url" id="youtube_url" name="youtube_url" class="form-control @error('youtube_url') is-invalid @enderror"
                                   value="{{ old('youtube_url', $settings->youtube_url) }}" placeholder="https://youtube.com/...">
                            @error('youtube_url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-0">
                            <label for="whatsapp_number" class="form-label"><i class="mdi mdi-whatsapp me-1"></i> WhatsApp Number</label>
                            <input type="text" id="whatsapp_number" name="whatsapp_number" class="form-control @error('whatsapp_number') is-invalid @enderror"
                                   value="{{ old('whatsapp_number', $settings->whatsapp_number) }}" placeholder="+1234567890">
                            @error('whatsapp_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- SEO & Pages -->
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">SEO & Pages</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="meta_keywords" class="form-label">Meta Keywords</label>
                                <input type="text" id="meta_keywords" name="meta_keywords" class="form-control @error('meta_keywords') is-invalid @enderror"
                                       value="{{ old('meta_keywords', is_array($settings->meta_keywords) ? implode(', ', $settings->meta_keywords) : $settings->meta_keywords) }}"
                                       placeholder="food, delivery, restaurant, online ordering">
                                <small class="text-muted">Separate keywords with commas</small>
                                @error('meta_keywords')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="about_content" class="form-label">About Page Content</label>
                            <textarea id="about_content" name="about_content" rows="5" class="form-control @error('about_content') is-invalid @enderror">{{ old('about_content', $settings->about_content) }}</textarea>
                            @error('about_content')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="terms_content" class="form-label">Terms & Conditions Content</label>
                            <textarea id="terms_content" name="terms_content" rows="5" class="form-control @error('terms_content') is-invalid @enderror">{{ old('terms_content', $settings->terms_content) }}</textarea>
                            @error('terms_content')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-0">
                            <label for="privacy_content" class="form-label">Privacy Policy Content</label>
                            <textarea id="privacy_content" name="privacy_content" rows="5" class="form-control @error('privacy_content') is-invalid @enderror">{{ old('privacy_content', $settings->privacy_content) }}</textarea>
                            @error('privacy_content')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <button type="submit" class="btn btn-primary">
                            <i class="mdi mdi-content-save me-1"></i> Save Settings
                        </button>
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary ms-2">Cancel</a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
