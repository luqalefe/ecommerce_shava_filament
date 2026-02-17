<?php

use Illuminate\Support\Facades\Route;

// Importação dos Controllers (mantidos para rotas ainda não migradas)
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SitemapController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// --- ROTAS PÚBLICAS DA LOJA ---

// Sitemap para SEO
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

// Home (Livewire)
Route::get('/', \App\Livewire\HomePage::class)->name('home');

// Sobre Nós (Livewire)
Route::get('/sobre-nos', \App\Livewire\AboutPage::class)->name('about');

// Páginas Legais
Route::get('/politica-de-privacidade', \App\Livewire\PrivacyPolicyPage::class)->name('privacy-policy');
Route::get('/termos-de-uso', \App\Livewire\TermsOfUsePage::class)->name('terms-of-use');

// MODIFICADO: Loja agora usa Livewire
Route::get('/loja', \App\Livewire\ProductList::class)->name('products.index');

// Categoria (ainda não migrado - manter)
Route::get('/categoria/{category:slug}', [CategoryController::class, 'show'])->name('category.show');

// Produto individual (ainda não migrado - manter)
Route::get('/produto/{product:slug}', [ProductController::class, 'show'])->name('product.show');

// Comprar Agora - movido para grupo auth (linha 59)

// --- ROTAS DO CARRINHO ---

// MODIFICADO: Carrinho agora usa Livewire
Route::get('/carrinho', \App\Livewire\CartPage::class)->name('cart.index');

// NOTA: As rotas POST/PATCH/DELETE do carrinho antigo podem ser removidas
// pois agora o AddToCart e CartPage Livewire fazem isso via wire:click
// Mas vamos manter comentadas por enquanto para não quebrar nada:
// Route::post('/carrinho/adicionar/{product}', [CartController::class, 'store'])->name('cart.store');
// Route::patch('/carrinho/atualizar/{item}', [CartController::class, 'update'])->name('cart.update');
// Route::delete('/carrinho/remover/{item}', [CartController::class, 'destroy'])->name('cart.destroy');

// --- ROTAS DE AUTENTICAÇÃO (Livewire Customizado) ---
Route::get('/verify-email', \App\Livewire\Auth\VerifyEmailCode::class)
    ->middleware('auth')
    ->name('verification.notice');

Route::get('/forgot-password', \App\Livewire\Auth\ResetPasswordWithCode::class)
    ->name('password.request');

Route::get('/change-password', \App\Livewire\Auth\ChangePassword::class)
    ->middleware('auth')
    ->name('password.change');

// --- ROTAS QUE EXIGEM AUTENTICAÇÃO ---
Route::middleware(['auth'])->group(function () {

    // Comprar Agora (adiciona ao carrinho e vai direto pro checkout)
    Route::post('/comprar-agora/{product}', [CheckoutController::class, 'buyNow'])->name('checkout.buyNow');

    // MODIFICADO: Checkout agora usa Livewire
    Route::get('/checkout', \App\Livewire\CheckoutPage::class)->name('checkout.index');

    // MANTER: Rota POST do checkout (o Livewire CheckoutPage vai fazer submit via wire:click)
    // Mas podemos manter por enquanto como fallback:
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');

    // MANTER: Rota da página de sucesso (não precisa ser Livewire)
    Route::get('/checkout/pedido-realizado', [CheckoutController::class, 'success'])->name('checkout.success');

    // MANTER: API endpoint para cálculo de frete (usado pelo CheckoutPage Livewire)
    Route::post('/checkout/calculate-shipping', [CheckoutController::class, 'calculateShipping'])
        ->name('checkout.shipping.calculate');

    // --- MEUS PEDIDOS (Livewire) ---
    Route::get('/meus-pedidos', \App\Livewire\MyOrdersList::class)
        ->name('orders.index');
    
    Route::get('/meus-pedidos/{order}', \App\Livewire\ViewOrderDetails::class)
        ->name('order.show');

    // --- PERFIL E DASHBOARD (Breeze) ---

    // Rota de Dashboard
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Rotas de Gerenciamento de Perfil (Livewire)
    Route::get('/profile', \App\Livewire\ProfilePage::class)->name('profile.edit');
    // Mantém as rotas PATCH e DELETE para compatibilidade, mas o Livewire faz tudo via wire:submit
    // Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    // Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

}); // Fecha o Route::middleware('auth')->group

// --- ROTAS DE AUTENTICAÇÃO (Livewire) ---
Route::middleware('guest')->group(function () {
    Route::get('/login', \App\Livewire\Auth\LoginPage::class)->name('login');
    Route::get('/register', \App\Livewire\Auth\RegisterPage::class)->name('register');
    
    // Rotas do Google Socialite
    Route::get('/auth/google/redirect', [\App\Http\Controllers\Auth\GoogleLoginController::class, 'redirectToGoogle'])
        ->name('auth.google.redirect');
    Route::get('/auth/google/callback', [\App\Http\Controllers\Auth\GoogleLoginController::class, 'handleGoogleCallback'])
        ->name('auth.google.callback');
});

// Rota de Logout (mantida do Breeze)
Route::post('/logout', [\App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

// Inclui outras rotas de autenticação (senha esquecida, etc.) do Breeze
// Inclui outras rotas de autenticação (senha esquecida, etc.) do Breeze
require __DIR__ . '/auth.php';