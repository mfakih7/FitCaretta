<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\Arr;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);
        Paginator::useBootstrapFive();

        $this->applyStoreSettingsOverrides();
    }

    private function applyStoreSettingsOverrides(): void
    {
        if (! Schema::hasTable('settings')) {
            return;
        }

        try {
            $rows = Setting::query()
                ->where('key', 'like', 'store.%')
                ->pluck('value', 'key');
        } catch (\Throwable) {
            // Avoid breaking the app during early boot/migrations.
            return;
        }

        if ($rows->isEmpty()) {
            return;
        }

        $overrides = [];

        foreach ($rows as $key => $value) {
            $path = ltrim(substr((string) $key, strlen('store.')), '.');
            if ($path === '') {
                continue;
            }

            $default = config('store.' . $path);
            $casted = $this->castSettingValue($value, $default);

            Arr::set($overrides, $path, $casted);
        }

        $merged = array_replace_recursive((array) config('store', []), $overrides);
        config(['store' => $merged]);
    }

    private function castSettingValue(mixed $value, mixed $default): mixed
    {
        if ($default === null) {
            return $value;
        }

        if (is_bool($default)) {
            return filter_var($value, FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE) ?? false;
        }

        if (is_int($default)) {
            return (int) $value;
        }

        if (is_float($default)) {
            return (float) $value;
        }

        return $value;
    }
}