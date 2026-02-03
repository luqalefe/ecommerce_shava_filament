<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use App\Models\Category;
use App\Models\Order;
use App\Observers\OrderObserver;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Forçar HTTPS se APP_URL usar HTTPS (importante para túneis como Serveo, ngrok, etc)
        $appUrl = config('app.url');
        if ($appUrl && str_starts_with($appUrl, 'https://')) {
            URL::forceScheme('https');
        }

        // Tenta compartilhar as categorias com a view do navbar
        // Tenta compartilhar as categorias com a view do navbar
        try {
            // Define um View Composer para a navbar
            // Removido Schema::hasTable para evitar erro de 'SYSTEM VERSIONED' em alguns drivers MySQL/MariaDB
            View::composer('layouts.partials.navbar', function ($view) {
                $view->with('globalCategories', Category::whereNull('parent_id')->with('children')->get());
            });
        } catch (\Exception $e) {
            Log::error("Erro no AppServiceProvider: " . $e->getMessage());
        }



        // Registrar Observer para notificações de mudança de status
        // O Observer agora está seguro: não bloqueia o save mesmo se houver erro de email
        Order::observe(OrderObserver::class);
    }
}