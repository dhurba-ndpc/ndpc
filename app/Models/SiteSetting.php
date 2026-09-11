<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    protected $fillable = [
        'group_name',
        'key_name',
        'value_en',
        'value_ne',
        'meta_attributes',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'meta_attributes' => 'array',
    ];

    /**
     * Retrieve a setting value cleanly with language fallback
     */
    public static function get(string $group, string $key, ?string $locale = null): ?string
    {
        $locale = $locale ?: app()->getLocale();
        $setting = static::where('group_name', $group)
            ->where('key_name', $key)
            ->where('is_active', true)
            ->first();

        if (!$setting) {
            return null;
        }

        return $locale === 'ne' ? ($setting->value_ne ?: $setting->value_en) : $setting->value_en;
    }
}
