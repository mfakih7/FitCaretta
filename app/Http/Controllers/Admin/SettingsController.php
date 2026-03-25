<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SettingsUpdateRequest;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function edit(): View
    {
        return view('admin.settings.edit');
    }

    public function update(SettingsUpdateRequest $request): RedirectResponse
    {
        $store = (array) $request->validated('store', []);

        $flat = $this->flattenArray($store, 'store');

        // Keep backward compatibility with both top-level store.* branding keys and store.brand.*.
        $flat = $this->mirrorBrandingKeys($flat);

        foreach ($flat as $key => $value) {
            if ($key === 'store.brand.show_name_with_logo') {
                // Checkbox: always persist as 1/0, never delete.
                Setting::updateOrCreate(
                    ['key' => $key],
                    ['value' => $value ? '1' : '0']
                );
                continue;
            }

            $string = is_null($value) ? '' : trim((string) $value);

            // Empty => remove override and fallback to config/store.php.
            if ($string === '') {
                Setting::query()->where('key', $key)->delete();
                continue;
            }

            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $string]
            );
        }

        return back()->with('success', 'Settings saved successfully.');
    }

    private function flattenArray(array $data, string $prefix): array
    {
        $out = [];

        foreach ($data as $k => $v) {
            $key = $prefix . '.' . $k;

            if (is_array($v)) {
                $out += $this->flattenArray($v, $key);
                continue;
            }

            $out[$key] = $v;
        }

        return $out;
    }

    private function mirrorBrandingKeys(array $flat): array
    {
        $map = [
            'logo_primary_path' => 'brand.logo_primary_path',
            'logo_mark_path' => 'brand.logo_mark_path',
            'logo_dark_path' => 'brand.logo_dark_path',
            'logo_light_path' => 'brand.logo_light_path',
            'logo_footer_path' => 'brand.logo_footer_path',
            'favicon_path' => 'brand.favicon_path',
            'brand_pdf_path' => 'brand.brand_pdf_path',
            'logo_alt' => 'brand.logo_alt',
        ];

        foreach ($map as $top => $brand) {
            $topKey = 'store.' . $top;
            $brandKey = 'store.' . $brand;

            if (array_key_exists($brandKey, $flat) && ! array_key_exists($topKey, $flat)) {
                $flat[$topKey] = $flat[$brandKey];
            }

            if (array_key_exists($topKey, $flat) && ! array_key_exists($brandKey, $flat)) {
                $flat[$brandKey] = $flat[$topKey];
            }
        }

        return $flat;
    }
}

