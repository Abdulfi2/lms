<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['group', 'key', 'value', 'type', 'label'];

    protected static function booted()
    {
        static::saved(fn() => Cache::forget('settings.all'));
        static::deleted(fn() => Cache::forget('settings.all'));
    }

    /**
     * Ambil satu nilai setting berdasarkan key, dengan fallback default.
     */
    public static function get(string $key, $default = null)
    {
        return static::allCached()->get($key, $default);
    }

    /**
     * Simpan/update satu nilai setting.
     */
    public static function set(string $key, $value): void
    {
        static::where('key', $key)->update(['value' => $value]);
        Cache::forget('settings.all');
    }

    /**
     * Semua setting sebagai [key => value], di-cache 1 jam supaya tidak query berulang
     * di setiap request (dipakai di layout/homepage).
     */
    protected static function allCached()
    {
        return Cache::remember('settings.all', 3600, function () {
            return static::query()->pluck('value', 'key');
        });
    }
}
