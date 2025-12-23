<?php

use App\Models\SiteSetting;

if (!function_exists('site_setting')) {
    /**
     * Helper global para acessar configurações do site
     * 
     * @param string $key Chave da configuração
     * @param mixed $default Valor padrão se não encontrar
     * @return mixed
     */
    function site_setting(string $key, mixed $default = null): mixed
    {
        return SiteSetting::get($key, $default);
    }
}

if (!function_exists('site_setting_url')) {
    /**
     * Helper para obter URL completa de um asset de configuração
     * 
     * @param string $key Chave da configuração
     * @param string|null $default Valor padrão
     * @return string|null
     */
    function site_setting_url(string $key, ?string $default = null): ?string
    {
        $value = SiteSetting::get($key, $default);
        
        if (empty($value)) {
            return $default ? asset($default) : null;
        }

        // Se já é URL completa
        if (str_starts_with($value, 'http')) {
            return $value;
        }

        // Se é path de storage
        if (str_starts_with($value, 'site-settings/')) {
            return asset('storage/' . $value);
        }

        // Para arquivos no public
        return asset($value);
    }
}
