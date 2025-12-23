<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SiteSetting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'label',
    ];

    /**
     * Tempo de cache em segundos (1 hora)
     */
    protected const CACHE_TTL = 3600;

    /**
     * Obtém o valor de uma configuração pelo key
     * Retorna o default se a tabela não existir (antes da migration)
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        try {
            return \Illuminate\Support\Facades\Cache::remember("site_setting_{$key}", self::CACHE_TTL, function () use ($key, $default) {
                $setting = self::where('key', $key)->first();
                return $setting ? $setting->value : $default;
            });
        } catch (\Exception $e) {
            // Tabela não existe ainda (antes da migration)
            return $default;
        }
    }

    /**
     * Define o valor de uma configuração
     */
    public static function set(string $key, mixed $value, ?string $type = null): bool
    {
        $setting = self::updateOrCreate(
            ['key' => $key],
            array_filter([
                'value' => $value,
                'type' => $type,
            ])
        );

        // Limpa o cache dessa configuração
        Cache::forget("site_setting_{$key}");

        return $setting->wasRecentlyCreated || $setting->wasChanged();
    }

    /**
     * Obtém todas as configurações de um grupo
     */
    public static function getGroup(string $group): array
    {
        return Cache::remember("site_settings_group_{$group}", self::CACHE_TTL, function () use ($group) {
            return self::where('group', $group)
                ->pluck('value', 'key')
                ->toArray();
        });
    }

    /**
     * Limpa todo o cache de configurações
     */
    public static function clearCache(): void
    {
        $settings = self::all();
        foreach ($settings as $setting) {
            Cache::forget("site_setting_{$setting->key}");
        }
        
        $groups = self::distinct()->pluck('group');
        foreach ($groups as $group) {
            Cache::forget("site_settings_group_{$group}");
        }
    }

    /**
     * Retorna o caminho completo para assets
     */
    public function getAssetUrlAttribute(): ?string
    {
        if (empty($this->value)) {
            return null;
        }

        // Se começa com http, já é uma URL completa
        if (str_starts_with($this->value, 'http')) {
            return $this->value;
        }

        // Se começa com storage/, usa asset
        if (str_starts_with($this->value, 'storage/')) {
            return asset($this->value);
        }

        // Para arquivos no public
        return asset($this->value);
    }
}
