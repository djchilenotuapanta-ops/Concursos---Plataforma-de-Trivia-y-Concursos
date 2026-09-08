<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    // Campos asignables
    protected $fillable = ['key','value'];

    // Usa timestamps
    public $timestamps = true;

    public static function getValue(string $key, $default = null)
    {
        // Obtener valor
        $row = static::where('key', $key)->first();
        return $row ? $row->value : $default;
    }

    public static function getInt(string $key, int $default): int
    {
        // Obtener entero
        $v = static::getValue($key, null);
        return is_null($v) ? $default : (int) $v;
    }

    public static function put(string $key, $value): void
    {
        // Guardar valor
        static::updateOrCreate(['key' => $key], ['value' => (string) $value]);
    }
}
