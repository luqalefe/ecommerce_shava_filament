<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class GoogleLoginController extends Controller
{
    /**
     * Redireciona para o Google OAuth
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle callback do Google OAuth
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            // Verifica se o usuário já existe
            $user = User::where('email', $googleUser->getEmail())->first();

            if ($user) {
                // Usuário existe, faz login
                Auth::login($user, true);
            } else {
                // Cria novo usuário
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'password' => Hash::make(uniqid()), // Senha aleatória (não será usada)
                    'is_admin' => false, // Garantir que novos usuários não sejam admin
                ]);

                Auth::login($user, true);
            }

            return redirect()->route('orders.index');
        } catch (\Exception $e) {
            return redirect()->route('login')
                ->with('error', 'Erro ao fazer login com Google. Tente novamente.');
        }
    }
}
