<?php

namespace App\Models;

use Illuminate\Support\Facades\Storage;

class Apariencia
{
    public static function getConfig()
    {
        $default = [
            'logo_path' => 'storage/imgs/Conexion_2025.png',
            'color_primario' => '#f97316',
            'color_secundario' => '#2563eb',
            'fondo_login' => 'arbol',
            'fade_gradient_start' => 'rgba(234, 90, 12, 0.63)',
            'fade_gradient_end' => 'rgba(2, 6, 23, 1)',
            'tema_gold' => '#c9a227',
            'tema_blue' => '#3b82f6',
            'bg_primary' => '#0a1628',
            'bg_secondary' => '#0f2044',
            'bg_sidebar' => '#080f20',
            'text_primary' => '#e2e8f0',
        ];

        if (Storage::disk('local')->exists('apariencia.json')) {
            $data = json_decode(Storage::disk('local')->get('apariencia.json'), true);
            return array_merge($default, $data ?? []);
        }

        return $default;
    }

    public static function setConfig($data)
    {
        $current = self::getConfig();
        $new = array_merge($current, $data);
        Storage::disk('local')->put('apariencia.json', json_encode($new, JSON_PRETTY_PRINT));
    }
}
