<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SystemSetting extends Model
{
    use Auditable;

    protected $fillable = [
        'group',
        'key',
        'value',
        'type',
    ];

    /**
     * Get a setting value by key with auto-caching.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        return Cache::rememberForever('sys_setting_' . $key, function () use ($key, $default) {
            $setting = static::where('key', $key)->first();
            if (!$setting) {
                return $default;
            }

            return match ($setting->type) {
                'integer' => (int) $setting->value,
                'boolean' => filter_var($setting->value, FILTER_VALIDATE_BOOLEAN),
                'json' => json_decode($setting->value, true) ?? $default,
                default => $setting->value,
            };
        });
    }

    /**
     * Set a setting value and invalidate cache.
     */
    public static function set(string $key, mixed $value, string $group = 'general', string $type = 'string'): static
    {
        $stringValue = match ($type) {
            'boolean' => $value ? '1' : '0',
            'json' => is_array($value) ? json_encode($value) : $value,
            default => (string) $value,
        };

        $setting = static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $stringValue,
                'group' => $group,
                'type' => $type,
            ]
        );

        Cache::forget('sys_setting_' . $key);
        Cache::forget('sys_settings_all_grouped');

        return $setting;
    }

    /**
     * Get all settings grouped by their category.
     */
    public static function getAllGrouped(): array
    {
        return Cache::rememberForever('sys_settings_all_grouped', function () {
            $all = static::all();
            $grouped = [];

            foreach ($all as $item) {
                $val = match ($item->type) {
                    'integer' => (int) $item->value,
                    'boolean' => filter_var($item->value, FILTER_VALIDATE_BOOLEAN),
                    'json' => json_decode($item->value, true),
                    default => $item->value,
                };
                $grouped[$item->group][$item->key] = $val;
            }

            return $grouped;
        });
    }

    /**
     * Clear all cached settings.
     */
    public static function clearSettingCache(): void
    {
        Cache::forget('sys_settings_all_grouped');
        $keys = static::pluck('key');
        foreach ($keys as $key) {
            Cache::forget('sys_setting_' . $key);
        }
    }
}
