<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class SettingsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if (!app()->runningInConsole() && Schema::hasTable('settings')) {
            // 1. Branding
            $branding = Setting::get('branding');
            if ($branding) {
                Config::set('app.name', $branding['site_name'] ?? config('app.name'));
                // Theme color is used via CSS variables in the layout
            }

            // 2. Email
            $email = Setting::get('email_settings');
            if ($email) {
                Config::set('mail.mailers.smtp.host', $email['smtp_host'] ?? config('mail.mailers.smtp.host'));
                Config::set('mail.from.address', $email['from_address'] ?? config('mail.from.address'));
            }

            // 3. Security
            $security = Setting::get('security_settings');
            if ($security) {
                Config::set('session.lifetime', $security['session_timeout'] ?? config('session.lifetime'));
            }

            // 4. API Limits
            $api = Setting::get('api_settings');
            if ($api) {
                // These are used in the RateLimiter within AppServiceProvider
                // We could also refactor AppServiceProvider to use these values
            }
        }
    }
}
