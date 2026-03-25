<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SettingsUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'store' => ['required', 'array'],

            // Store Info
            'store.name' => ['nullable', 'string', 'max:120'],
            'store.tagline' => ['nullable', 'string', 'max:255'],
            'store.short_description' => ['nullable', 'string', 'max:255'],
            'store.admin_name' => ['nullable', 'string', 'max:120'],

            // Contact
            'store.contact_email' => ['nullable', 'email', 'max:190'],
            'store.support_email' => ['nullable', 'email', 'max:190'],
            'store.phone' => ['nullable', 'string', 'max:40'],
            'store.whatsapp_number' => ['nullable', 'string', 'max:40'],

            // Location
            'store.address' => ['nullable', 'string', 'max:255'],
            'store.country' => ['nullable', 'string', 'max:120'],
            'store.city' => ['nullable', 'string', 'max:120'],

            // Currency
            'store.currency_code' => ['nullable', 'string', 'max:10'],
            'store.currency_symbol' => ['nullable', 'string', 'max:10'],

            // Topbar / Footer / Hero
            'store.topbar_shipping_text' => ['nullable', 'string', 'max:255'],
            'store.topbar_promo_text' => ['nullable', 'string', 'max:255'],
            'store.footer_copyright_text' => ['nullable', 'string', 'max:255'],
            'store.footer_note' => ['nullable', 'string', 'max:255'],
            'store.hero_title' => ['nullable', 'string', 'max:255'],

            // Branding (we support both store.* and store.brand.* for compatibility)
            'store.logo_primary_path' => ['nullable', 'string', 'max:255'],
            'store.logo_mark_path' => ['nullable', 'string', 'max:255'],
            'store.logo_dark_path' => ['nullable', 'string', 'max:255'],
            'store.logo_light_path' => ['nullable', 'string', 'max:255'],
            'store.logo_footer_path' => ['nullable', 'string', 'max:255'],
            'store.favicon_path' => ['nullable', 'string', 'max:255'],
            'store.brand_pdf_path' => ['nullable', 'string', 'max:255'],
            'store.logo_alt' => ['nullable', 'string', 'max:255'],

            'store.brand' => ['sometimes', 'array'],
            'store.brand.logo_primary_path' => ['nullable', 'string', 'max:255'],
            'store.brand.logo_mark_path' => ['nullable', 'string', 'max:255'],
            'store.brand.logo_dark_path' => ['nullable', 'string', 'max:255'],
            'store.brand.logo_light_path' => ['nullable', 'string', 'max:255'],
            'store.brand.logo_footer_path' => ['nullable', 'string', 'max:255'],
            'store.brand.favicon_path' => ['nullable', 'string', 'max:255'],
            'store.brand.brand_pdf_path' => ['nullable', 'string', 'max:255'],
            'store.brand.logo_alt' => ['nullable', 'string', 'max:255'],
            'store.brand.show_name_with_logo' => ['sometimes', 'boolean'],

            // Social
            'store.social' => ['sometimes', 'array'],
            'store.social.facebook' => ['nullable', 'string', 'max:255'],
            'store.social.instagram' => ['nullable', 'string', 'max:255'],
            'store.social.instagram_handle' => ['nullable', 'string', 'max:255'],
            'store.social.tiktok' => ['nullable', 'string', 'max:255'],
            'store.social.x' => ['nullable', 'string', 'max:255'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $store = (array) $this->input('store', []);
        $brand = (array) ($store['brand'] ?? []);

        // Unchecked checkbox isn't sent by browsers; make it explicit.
        $brand['show_name_with_logo'] = (bool) ($brand['show_name_with_logo'] ?? false);
        $store['brand'] = $brand;

        $this->merge(['store' => $store]);
    }
}

