<?php

namespace App\Providers; // Corrigido: Namespace estava como App.Providers

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use App\Models\Category; // Garanta que o Model Category está no namespace correto (App\Models)

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Geralmente vazio para aplicações simples
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Tenta compartilhar as categorias com a view do navbar
        try {
            // Verifica se a tabela 'categories' existe no banco de dados.
            // Essencial para evitar erros durante comandos como 'migrate:fresh'.
            if (Schema::hasTable('categories')) {

                // Define um View Composer que será executado APENAS quando a view 'layouts.partials.navbar' for renderizada.
                View::composer('layouts.partials.navbar', function ($view) {

                    // Busca as categorias PAI (onde parent_id é null)
                    // e já carrega seus relacionamentos 'children' para otimizar (evita N+1 queries).
                    $globalCategories = Category::whereNull('parent_id')
                                                ->with('children')
                                                ->get();

                    // Envia a variável $globalCategories para a view ('layouts.partials.navbar').
                    $view->with('globalCategories', $globalCategories);
                });
            }
        } catch (\Exception $e) {
            // Se qualquer erro inesperado ocorrer (ex: problema de conexão com BD),
            // registra o erro no log para análise posterior.
            Log::error("Erro no AppServiceProvider ao buscar categorias globais para o navbar: " . $e->getMessage());
            // A view do navbar simplesmente não receberá a variável $globalCategories neste caso.
        }
    }
}