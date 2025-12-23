<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ChangePassword extends Component
{
    public $current_password = '';
    public $password = '';
    public $password_confirmation = '';
    public $error = '';
    public $success = '';

    protected $rules = [
        'current_password' => 'required',
        'password' => 'required|min:8|confirmed',
    ];

    public function mount()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
    }

    public function updatePassword()
    {
        $this->validate();
        $this->error = '';
        $this

->success = '';

        $user = Auth::user();

        // Verificar senha atual
        if (!Hash::check($this->current_password, $user->password)) {
            $this->error = 'Senha atual incorreta.';
            return;
        }

        // Atualizar senha
        $user->password = Hash::make($this->password);
        $user->save();

        $this->success = 'Senha atualizada com sucesso!';
        
        // Limpar campos
        $this->reset(['current_password', 'password', 'password_confirmation']);
    }

    public function render()
    {
        return view('livewire.auth.change-password')
            ->layout('layouts.app');
    }
}
