<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

class GoogleLoginController extends Controller
{
    /**
     * Redireciona para o Google OAuth
     */
    public function redirectToGoogle()
    {
        try {
            // Verificar se as credenciais estão configuradas
            $clientId = config('services.google.client_id');
            $clientSecret = config('services.google.client_secret');
            
            if (empty($clientId) || empty($clientSecret)) {
                Log::error('Google OAuth não configurado: client_id ou client_secret ausentes');
                return redirect()->route('login')
                    ->with('error', 'Login com Google não está configurado. Entre em contato com o suporte.');
            }

            return Socialite::driver('google')->redirect();
        } catch (\Exception $e) {
            Log::error('Erro ao redirecionar para Google OAuth', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return redirect()->route('login')
                ->with('error', 'Erro ao iniciar login com Google. Tente novamente.');
        }
    }

    /**
     * Handle callback do Google OAuth
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            if (!$googleUser || !$googleUser->getEmail()) {
                throw new \Exception('Não foi possível obter dados do usuário do Google');
            }

            // Verifica se o usuário já existe
            $user = User::where('email', $googleUser->getEmail())->first();

            if ($user) {
                // Usuário existe, faz login
                Auth::login($user, true);
                Log::info('Usuário logado via Google OAuth', ['user_id' => $user->id, 'email' => $user->email]);
            } else {
                // Cria novo usuário
                $user = User::create([
                    'name' => $googleUser->getName() ?? 'Usuário Google',
                    'email' => $googleUser->getEmail(),
                    'password' => Hash::make(uniqid()), // Senha aleatória (não será usada)
                    'is_admin' => false, // Garantir que novos usuários não sejam admin
                    'email_verified_at' => now(), // Google já verifica o email
                ]);

                Auth::login($user, true);
                Log::info('Novo usuário criado via Google OAuth', ['user_id' => $user->id, 'email' => $user->email]);
            }

            return redirect()->route('orders.index')
                ->with('success', 'Login realizado com sucesso!');
        } catch (\Illuminate\Contracts\Container\BindingResolutionException $e) {
            Log::error('Erro de configuração do Google OAuth', [
                'error' => $e->getMessage(),
            ]);
            
            return redirect()->route('login')
                ->with('error', 'Login com Google não está configurado corretamente. Verifique as credenciais no arquivo .env');
        } catch (\Exception $e) {
            Log::error('Erro ao processar callback do Google OAuth', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return redirect()->route('login')
                ->with('error', 'Erro ao fazer login com Google: ' . $e->getMessage());
        }
    }
}
