<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Rules\Cpf;

#[Layout('components.layouts.app')]
class ProfilePage extends Component
{
    // Dados do Perfil
    public $name = '';
    public $email = '';
    public $celular = '';
    public $cpf = '';

    // Dados de Senha
    public $current_password = '';
    public $password = '';
    public $password_confirmation = '';

    // Estado
    public $activeTab = 'profile'; // 'profile', 'password', 'delete'
    public $showSuccessMessage = false;

    public function mount()
    {
        $user = Auth::user();
        $this->name = $user->name;
        $this->email = $user->email;
        $this->celular = $user->celular ?? '';
        $this->cpf = $user->cpf ?? '';
    }

    public function updateProfile()
    {
        $this->validate([
            'name' => 'required|string|min:3|max:255',
            'email' => 'required|email|unique:users,email,' . Auth::id(),
            'celular' => 'nullable|string|max:20',
            'cpf' => ['nullable', 'string', new Cpf],
        ], [
            'name.required' => 'O campo nome é obrigatório.',
            'name.min' => 'O nome deve ter no mínimo 3 caracteres.',
            'email.required' => 'O campo e-mail é obrigatório.',
            'email.email' => 'Digite um e-mail válido.',
            'email.unique' => 'Este e-mail já está cadastrado.',
        ]);

        $user = Auth::user();
        $user->update([
            'name' => $this->name,
            'email' => $this->email,
            'celular' => $this->celular,
            'cpf' => $this->cpf,
        ]);

        $this->showSuccessMessage = true;
        $this->dispatch('profile-updated');
        
        session()->flash('message', 'Perfil atualizado com sucesso!');
    }

    public function updatePassword()
    {
        $this->validate([
            'current_password' => 'required|current_password',
            'password' => ['required', 'confirmed', Password::defaults()],
        ], [
            'current_password.required' => 'A senha atual é obrigatória.',
            'current_password.current_password' => 'A senha atual está incorreta.',
            'password.required' => 'A nova senha é obrigatória.',
            'password.confirmed' => 'As senhas não coincidem.',
        ]);

        $user = Auth::user();
        $user->update([
            'password' => Hash::make($this->password),
        ]);

        // Limpa os campos
        $this->current_password = '';
        $this->password = '';
        $this->password_confirmation = '';

        $this->showSuccessMessage = true;
        $this->dispatch('password-updated');
        
        session()->flash('message', 'Senha atualizada com sucesso!');
    }

    public function deleteAccount()
    {
        $this->validate([
            'current_password' => 'required|current_password',
        ], [
            'current_password.required' => 'A senha é obrigatória para deletar a conta.',
            'current_password.current_password' => 'A senha está incorreta.',
        ]);

        $user = Auth::user();
        Auth::logout();
        $user->delete();

        session()->invalidate();
        session()->regenerateToken();

        return redirect()->route('home')->with('message', 'Sua conta foi deletada com sucesso.');
    }

    public function render()
    {
        return view('livewire.profile-page');
    }
}
