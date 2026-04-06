<?php

namespace App\Providers;

use App\Models\Setting;
use App\Models\Content\AboutPage;
use App\Models\FeedbackSetting;
use Illuminate\Support\Arr;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;

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
        $this->composeFrontendAboutNav();
        $this->composeFrontendFeedbackNav();
    }

    private function composeFrontendAboutNav(): void
    {
        View::composer('frontend.partials.header', function ($view) {
            $enabled = false;
            try {
                if (Schema::hasTable('about_pages')) {
                    $enabled = AboutPage::query()->visible()->exists();
                }
            } catch (\Throwable) {
                $enabled = false;
            }

            $view->with('showAboutNav', $enabled);
        });
    }

    private function composeFrontendFeedbackNav(): void
    {
        View::composer('frontend.partials.header', function ($view) {
            $enabled = false;
            try {
                if (Schema::hasTable('feedback_settings')) {
                    $enabled = (bool) FeedbackSetting::query()->value('is_enabled');
                }
            } catch (\Throwable) {
                $enabled = false;
            }

            $view->with('showFeedbackNav', $enabled);
        });
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