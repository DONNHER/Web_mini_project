<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class SettingController extends Controller
{
    public function index()
    {
        $settings = [
            'branding' => Setting::get('branding', [
                'site_name' => config('app.name'),
                'theme_color' => '#C06C3E',
            ]),
            'email' => Setting::get('email_settings', [
                'smtp_host' => env('MAIL_HOST'),
                'smtp_port' => env('MAIL_PORT'),
                'from_address' => env('MAIL_FROM_ADDRESS'),
            ]),
            'security' => Setting::get('security_settings', [
                'session_timeout' => config('session.lifetime'),
                'mfa_enforced' => false,
                'min_password_length' => 8,
            ]),
            'backup' => Setting::get('backup_schedule', [
                'database' => 'weekly',
                'files' => 'weekly',
                'retention_days' => 30,
            ]),
            'api' => Setting::get('api_settings', [
                'rate_limit_standard' => 60,
                'rate_limit_premium' => 300,
            ]),
            'maintenance' => [
                'enabled' => app()->isDownForMaintenance(),
                'message' => 'System is under maintenance. Please check back later.',
            ]
        ];

        return view('admin.settings.index', $settings);
    }

    public function update(Request $request)
    {
        // 1. Branding
        if ($request->has('branding')) {
            Setting::set('branding', $request->branding, 'json');
        }

        // 2. Email
        if ($request->has('email')) {
            Setting::set('email_settings', $request->email, 'json');
        }

        // 3. Security
        if ($request->has('security')) {
            Setting::set('security_settings', $request->security, 'json');
        }

        // 4. Backup
        if ($request->has('backup')) {
            Setting::set('backup_schedule', $request->backup, 'json');
        }

        // 5. API
        if ($request->has('api')) {
            Setting::set('api_settings', $request->api, 'json');
        }

        // 6. Maintenance Mode (Requirement 9.6)
        if ($request->has('maintenance')) {
            if ($request->maintenance['enabled'] == '1' && !app()->isDownForMaintenance()) {
                Artisan::call('down', [
                    '--message' => $request->maintenance['message'] ?? 'Maintenance Mode Active'
                ]);
            } elseif ($request->maintenance['enabled'] == '0' && app()->isDownForMaintenance()) {
                Artisan::call('up');
            }
        }

        return back()->with('success', 'System parameters updated successfully.');
    }
}
