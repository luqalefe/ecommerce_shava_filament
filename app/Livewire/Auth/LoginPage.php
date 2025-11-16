<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class LoginPage extends Component
{
    public $email = '';
    public $password = '';
    public $remember = false;

    public function mount()
    {
        // Se já estiver logado, redireciona
        if (Auth::check()) {
            return redirect()->route('orders.index');
        }
    }

    protected $rules = [
        'email' => 'required|email',
        'password' => 'required|min:8',
    ];

    protected $messages = [
        'email.required' => 'O campo e-mail é obrigatório.',
        'email.email' => 'Digite um e-mail válido.',
        'password.required' => 'O campo senha é obrigatório.',
        'password.min' => 'A senha deve ter no mínimo 8 caracteres.',
    ];

    public function login()
    {
        $this->validate();

        if (Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            session()->regenerate();
            return redirect()->intended(route('orders.index'));
        }

        $this->addError('email', 'As credenciais fornecidas estão incorretas.');
    }

    public function render()
    {
        return view('livewire.auth.login-page');
    }
}
