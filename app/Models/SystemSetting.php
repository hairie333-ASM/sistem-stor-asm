<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'category',
        'description',
    ];

    public static function get(string $key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    public static function set(string $key, $value, $category = 'GENERAL', $description = null)
    {
        return static::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'category' => $category, 'description' => $description]
        );
    }
}
