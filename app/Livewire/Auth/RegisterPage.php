<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class RegisterPage extends Component
{
    public $name = '';
    public $email = '';
    public $password = '';
    public $password_confirmation = '';

    public function mount()
    {
        // Se já estiver logado, redireciona
        if (Auth::check()) {
            return redirect()->route('orders.index');
        }
    }

    protected $rules = [
        'name' => 'required|string|min:3|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|min:8|confirmed',
    ];

    protected $messages = [
        'name.required' => 'O campo nome é obrigatório.',
        'name.min' => 'O nome deve ter no mínimo 3 caracteres.',
        'email.required' => 'O campo e-mail é obrigatório.',
        'email.email' => 'Digite um e-mail válido.',
        'email.unique' => 'Este e-mail já está cadastrado.',
        'password.required' => 'O campo senha é obrigatório.',
        'password.min' => 'A senha deve ter no mínimo 8 caracteres.',
        'password.confirmed' => 'As senhas não coincidem.',
    ];

    public function register()
    {
        $this->validate();

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'is_admin' => false, // Garantir que novos usuários não sejam admin
        ]);

        Auth::login($user, true);
        session()->regenerate();

        return redirect()->route('orders.index');
    }

    public function render()
    {
        return view('livewire.auth.register-page');
    }
}
