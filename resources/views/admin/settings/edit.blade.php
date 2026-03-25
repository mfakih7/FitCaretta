@extends('layouts.admin')

@section('title', 'Settings')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h4 mb-0">Settings</h1>
            <div class="text-muted small">Business/store settings. Leave fields blank to use defaults from <code>config/store.php</code>.</div>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.settings.update') }}" class="d-flex flex-column gap-3">
        @csrf
        @method('PUT')

        <div class="card shadow-sm">
            <div class="card-body">
                <h2 class="h6 mb-3">Store Info</h2>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Name</label>
                        <input type="text" name="store[name]" class="form-control" value="{{ old('store.name', config('store.name')) }}">
                        @error('store.name')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Admin Name</label>
                        <input type="text" name="store[admin_name]" class="form-control" value="{{ old('store.admin_name', config('store.admin_name')) }}">
                        @error('store.admin_name')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Tagline</label>
                        <input type="text" name="store[tagline]" class="form-control" value="{{ old('store.tagline', config('store.tagline')) }}">
                        @error('store.tagline')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Short Description</label>
                        <input type="text" name="store[short_description]" class="form-control" value="{{ old('store.short_description', config('store.short_description')) }}">
                        @error('store.short_description')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <h2 class="h6 mb-3">Contact</h2>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Contact Email</label>
                        <input type="email" name="store[contact_email]" class="form-control" value="{{ old('store.contact_email', config('store.contact_email')) }}">
                        @error('store.contact_email')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Support Email</label>
                        <input type="email" name="store[support_email]" class="form-control" value="{{ old('store.support_email', config('store.support_email')) }}">
                        @error('store.support_email')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Phone</label>
                        <input type="text" name="store[phone]" class="form-control" value="{{ old('store.phone', config('store.phone')) }}">
                        @error('store.phone')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">WhatsApp Number</label>
                        <input type="text" name="store[whatsapp_number]" class="form-control" value="{{ old('store.whatsapp_number', config('store.whatsapp_number')) }}">
                        @error('store.whatsapp_number')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <h2 class="h6 mb-3">Location</h2>
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label">Address</label>
                        <input type="text" name="store[address]" class="form-control" value="{{ old('store.address', config('store.address')) }}">
                        @error('store.address')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Country</label>
                        <input type="text" name="store[country]" class="form-control" value="{{ old('store.country', config('store.country')) }}">
                        @error('store.country')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">City</label>
                        <input type="text" name="store[city]" class="form-control" value="{{ old('store.city', config('store.city')) }}">
                        @error('store.city')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <h2 class="h6 mb-3">Currency</h2>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Currency Code</label>
                        <input type="text" name="store[currency_code]" class="form-control" value="{{ old('store.currency_code', config('store.currency_code')) }}" placeholder="USD">
                        @error('store.currency_code')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Currency Symbol</label>
                        <input type="text" name="store[currency_symbol]" class="form-control" value="{{ old('store.currency_symbol', config('store.currency_symbol')) }}" placeholder="$">
                        @error('store.currency_symbol')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <h2 class="h6 mb-3">Topbar / Footer / Hero</h2>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Topbar Shipping Text</label>
                        <input type="text" name="store[topbar_shipping_text]" class="form-control" value="{{ old('store.topbar_shipping_text', config('store.topbar_shipping_text')) }}">
                        @error('store.topbar_shipping_text')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Topbar Promo Text</label>
                        <input type="text" name="store[topbar_promo_text]" class="form-control" value="{{ old('store.topbar_promo_text', config('store.topbar_promo_text')) }}">
                        @error('store.topbar_promo_text')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Footer Copyright</label>
                        <input type="text" name="store[footer_copyright_text]" class="form-control" value="{{ old('store.footer_copyright_text', config('store.footer_copyright_text')) }}">
                        @error('store.footer_copyright_text')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Footer Note</label>
                        <input type="text" name="store[footer_note]" class="form-control" value="{{ old('store.footer_note', config('store.footer_note')) }}">
                        @error('store.footer_note')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label">Hero Title</label>
                        <input type="text" name="store[hero_title]" class="form-control" value="{{ old('store.hero_title', config('store.hero_title')) }}">
                        @error('store.hero_title')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <h2 class="h6 mb-3">Branding</h2>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Primary Logo Path</label>
                        <input type="text" name="store[brand][logo_primary_path]" class="form-control" value="{{ old('store.brand.logo_primary_path', config('store.brand.logo_primary_path')) }}" placeholder="assets/brand/logo.png">
                        @error('store.brand.logo_primary_path')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Logo Mark Path</label>
                        <input type="text" name="store[brand][logo_mark_path]" class="form-control" value="{{ old('store.brand.logo_mark_path', config('store.brand.logo_mark_path')) }}" placeholder="assets/brand/logo-mark.png">
                        @error('store.brand.logo_mark_path')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Dark Logo Path</label>
                        <input type="text" name="store[brand][logo_dark_path]" class="form-control" value="{{ old('store.brand.logo_dark_path', config('store.brand.logo_dark_path')) }}">
                        @error('store.brand.logo_dark_path')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Light Logo Path</label>
                        <input type="text" name="store[brand][logo_light_path]" class="form-control" value="{{ old('store.brand.logo_light_path', config('store.brand.logo_light_path')) }}">
                        @error('store.brand.logo_light_path')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Footer Logo Path</label>
                        <input type="text" name="store[brand][logo_footer_path]" class="form-control" value="{{ old('store.brand.logo_footer_path', config('store.brand.logo_footer_path')) }}">
                        @error('store.brand.logo_footer_path')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Favicon Path</label>
                        <input type="text" name="store[brand][favicon_path]" class="form-control" value="{{ old('store.brand.favicon_path', config('store.brand.favicon_path')) }}" placeholder="assets/brand/favicon.png">
                        @error('store.brand.favicon_path')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Brand PDF Path</label>
                        <input type="text" name="store[brand][brand_pdf_path]" class="form-control" value="{{ old('store.brand.brand_pdf_path', config('store.brand.brand_pdf_path')) }}">
                        @error('store.brand.brand_pdf_path')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Logo Alt Text</label>
                        <input type="text" name="store[brand][logo_alt]" class="form-control" value="{{ old('store.brand.logo_alt', config('store.brand.logo_alt')) }}">
                        @error('store.brand.logo_alt')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" id="show_name_with_logo"
                                   name="store[brand][show_name_with_logo]" value="1"
                                   @checked((bool) old('store.brand.show_name_with_logo', config('store.brand.show_name_with_logo')))>
                            <label class="form-check-label" for="show_name_with_logo">Show store name with logo</label>
                        </div>
                        @error('store.brand.show_name_with_logo')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <h2 class="h6 mb-3">Social</h2>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Facebook</label>
                        <input type="text" name="store[social][facebook]" class="form-control" value="{{ old('store.social.facebook', config('store.social.facebook')) }}">
                        @error('store.social.facebook')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Instagram URL</label>
                        <input type="text" name="store[social][instagram]" class="form-control" value="{{ old('store.social.instagram', config('store.social.instagram')) }}">
                        @error('store.social.instagram')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Instagram Handle</label>
                        <input type="text" name="store[social][instagram_handle]" class="form-control" value="{{ old('store.social.instagram_handle', config('store.social.instagram_handle')) }}">
                        @error('store.social.instagram_handle')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">TikTok</label>
                        <input type="text" name="store[social][tiktok]" class="form-control" value="{{ old('store.social.tiktok', config('store.social.tiktok')) }}">
                        @error('store.social.tiktok')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">X (Twitter)</label>
                        <input type="text" name="store[social][x]" class="form-control" value="{{ old('store.social.x', config('store.social.x')) }}">
                        @error('store.social.x')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex gap-2">
            <button class="btn btn-primary" type="submit">Save Settings</button>
            <a href="{{ route('admin.settings.edit') }}" class="btn btn-outline-secondary">Reset</a>
        </div>
    </form>
@endsection

