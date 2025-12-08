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
use AbacatePay\Clients\Client; // <<< ADICIONE ESTE IMPORT

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
        try {
            // Verifica se a tabela 'categories' existe no banco de dados.
            if (Schema::hasTable('categories')) {

                // Define um View Composer que será executado APENAS quando a view 'layouts.partials.navbar' for renderizada.
                View::composer('layouts.partials.navbar', function ($view) {

                    // Busca as categorias PAI (onde parent_id é null)
                    $globalCategories = Category::whereNull('parent_id')
                                                ->with('children')
                                                ->get();

                    // Envia a variável $globalCategories para a view ('layouts.partials.navbar').
                    $view->with('globalCategories', $globalCategories);
                });
            }
        } catch (\Exception $e) {
            // Se qualquer erro inesperado ocorrer (ex: problema de conexão com BD),
            Log::error("Erro no AppServiceProvider ao buscar categorias globais para o navbar: " . $e->getMessage());
        }


        // --- INÍCIO DA CONFIGURAÇÃO ABACATE PAY ---
        // Configura a chave da API para todo o aplicativo
        // Nós lemos de config/services.php por performance (cache de config)
        if (config('services.abacatepay.key')) {
            Client::setToken(config('services.abacatepay.key'));
        }
        // --- FIM DA CONFIGURAÇÃO ABACATE PAY ---

        // Registrar Observer para notificações de mudança de status
        // O Observer agora está seguro: não bloqueia o save mesmo se houver erro de email
        Order::observe(OrderObserver::class);
    }
}