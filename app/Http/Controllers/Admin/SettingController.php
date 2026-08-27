<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AuditLogAction;
use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class SettingController extends Controller
{
    /**
     * Display the settings management view.
     */
    public function index()
    {
        $settings = SystemSetting::getAllGrouped();

        $systemInfo = [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'environment' => app()->environment(),
            'app_debug' => config('app.debug') ? 'Enabled (ON)' : 'Disabled (OFF)',
            'database_driver' => strtoupper(config('database.default')),
            'cache_driver' => strtoupper(config('cache.default')),
            'queue_connection' => strtoupper(config('queue.default')),
            'memory_usage' => round(memory_get_usage(true) / 1024 / 1024, 2) . ' MB',
            'max_execution_time' => ini_get('max_execution_time') . ' Detik',
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Nginx/PHP-FPM (Docker)',
            'timezone' => config('app.timezone', 'Asia/Jakarta'),
        ];

        return view('admin.system.settings', compact('settings', 'systemInfo'));
    }

    /**
     * Update settings by category group.
     */
    public function update(Request $request)
    {
        $group = $request->input('group', 'general');

        if ($group === 'general') {
            $request->validate([
                'store_name' => 'required|string|max:100',
                'store_tagline' => 'nullable|string|max:255',
                'store_email' => 'required|email|max:150',
                'store_phone' => 'required|string|max:30',
                'store_address' => 'required|string|max:500',
                'warehouse_city' => 'nullable|string|max:100',
                'warehouse_latitude' => 'nullable|string|max:50',
                'warehouse_longitude' => 'nullable|string|max:50',
                'map_zoom_level' => 'nullable|integer|min:1|max:20',
            ]);

            SystemSetting::set('store_name', $request->store_name, 'general', 'string');
            SystemSetting::set('store_tagline', $request->store_tagline, 'general', 'string');
            SystemSetting::set('store_email', $request->store_email, 'general', 'string');
            SystemSetting::set('store_phone', $request->store_phone, 'general', 'string');
            SystemSetting::set('store_address', $request->store_address, 'general', 'string');
            SystemSetting::set('warehouse_city', $request->warehouse_city, 'general', 'string');
            if ($request->filled('warehouse_latitude')) {
                SystemSetting::set('warehouse_latitude', $request->warehouse_latitude, 'general', 'string');
            }
            if ($request->filled('warehouse_longitude')) {
                SystemSetting::set('warehouse_longitude', $request->warehouse_longitude, 'general', 'string');
            }
            if ($request->filled('map_zoom_level')) {
                SystemSetting::set('map_zoom_level', $request->map_zoom_level, 'general', 'integer');
            }

            $desc = 'Memperbarui informasi profil, kontak toko, dan koordinat peta gudang';
        } elseif ($group === 'transaction') {
            $request->validate([
                'currency_code' => 'required|string|max:10',
                'currency_symbol' => 'required|string|max:10',
                'tax_percentage' => 'required|integer|min:0|max:100',
                'payment_expiry_hours' => 'required|integer|min:1|max:168',
                'min_order_amount' => 'required|integer|min:0',
            ]);

            SystemSetting::set('currency_code', $request->currency_code, 'transaction', 'string');
            SystemSetting::set('currency_symbol', $request->currency_symbol, 'transaction', 'string');
            SystemSetting::set('tax_percentage', $request->tax_percentage, 'transaction', 'integer');
            SystemSetting::set('payment_expiry_hours', $request->payment_expiry_hours, 'transaction', 'integer');
            SystemSetting::set('min_order_amount', $request->min_order_amount, 'transaction', 'integer');

            $desc = 'Memperbarui konfigurasi transaksi, pajak, dan batas waktu pembayaran';
        } elseif ($group === 'system') {
            $maintenance = $request->boolean('maintenance_mode');
            $orderNotif = $request->boolean('order_notification_email');

            SystemSetting::set('maintenance_mode', $maintenance, 'system', 'boolean');
            SystemSetting::set('order_notification_email', $orderNotif, 'system', 'boolean');

            $desc = 'Memperbarui konfigurasi pemeliharaan sistem & notifikasi';
        }

        AuditLog::log(
            AuditLogAction::UPDATE,
            $desc ?? 'Memperbarui pengaturan sistem',
            null,
            ['group' => $group, 'inputs' => $request->except(['_token', 'group'])]
        );

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Pengaturan berhasil disimpan dan cache sistem diperbarui.',
            ]);
        }

        return back()->with('success', 'Pengaturan berhasil disimpan.');
    }

    /**
     * Execute cache clearing commands.
     */
    public function clearCache(Request $request)
    {
        $type = $request->input('type', 'all');

        try {
            switch ($type) {
                case 'app':
                    Artisan::call('cache:clear');
                    SystemSetting::clearSettingCache();
                    $msg = 'Cache aplikasi dan setting berhasil dibersihkan.';
                    break;
                case 'view':
                    Artisan::call('view:clear');
                    $msg = 'Kompilasi cache Blade view berhasil dibersihkan.';
                    break;
                case 'route':
                    Artisan::call('route:clear');
                    $msg = 'Cache rute aplikasi berhasil dibersihkan.';
                    break;
                case 'config':
                    Artisan::call('config:clear');
                    $msg = 'Cache file konfigurasi berhasil dibersihkan.';
                    break;
                case 'all':
                default:
                    Artisan::call('optimize:clear');
                    SystemSetting::clearSettingCache();
                    $msg = 'Seluruh cache sistem (App, View, Route, Config) berhasil dibersihkan secara total.';
                    break;
            }

            AuditLog::log(
                AuditLogAction::UPDATE,
                "Menjalankan pembersihan cache sistem: {$type}",
                null,
                ['cache_type' => $type]
            );

            return response()->json([
                'success' => true,
                'message' => $msg,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membersihkan cache: ' . $e->getMessage(),
            ], 500);
        }
    }
}
