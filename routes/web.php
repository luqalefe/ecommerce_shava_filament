<?php

use Illuminate\Support\Facades\Route;

// Importação dos Controllers
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ProfileController; // Controller do Breeze para perfil

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
| Aqui registramos as rotas acessíveis via navegador.
*/

// --- ROTAS PÚBLICAS DA LOJA ---
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/loja', [ProductController::class, 'index'])->name('products.index');
Route::get('/categoria/{category:slug}', [CategoryController::class, 'show'])->name('category.show');
Route::get('/produto/{product:slug}', [ProductController::class, 'show'])->name('product.show');
Route::post('/comprar-agora/{product}', [CheckoutController::class, 'buyNow'])->name('checkout.buyNow');

// --- ROTAS DO CARRINHO ---
Route::get('/carrinho', [CartController::class, 'index'])->name('cart.index');
Route::post('/carrinho/adicionar/{product}', [CartController::class, 'store'])->name('cart.store');
Route::patch('/carrinho/atualizar/{item}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/carrinho/remover/{item}', [CartController::class, 'destroy'])->name('cart.destroy');

// --- ROTAS QUE EXIGEM AUTENTICAÇÃO ---
Route::middleware(['auth'])->group(function () {
    // Rota do Checkout (Exige login para acessar)
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    // A rota POST para processar o checkout será adicionada aqui depois
    // Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');

    // Rota de Dashboard (Página padrão após login - do Breeze)
    // Opcional: Adicionar ->middleware(['verified']) se quiser exigir verificação de e-mail
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Rotas de Gerenciamento de Perfil (do Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Adicione aqui outras rotas que devem exigir login no futuro (ex: Meus Pedidos)

}); // Fecha o Route::middleware('auth')->group

// Inclui as rotas de autenticação (Login, Registro, Senha Esquecida, etc.) definidas pelo Breeze.
// Este arquivo está em routes/auth.php
require __DIR__.'/auth.php';