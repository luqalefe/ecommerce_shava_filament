<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Services\VerificationCodeService;
use App\Models\VerificationCode;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

#[Layout('components.layouts.app')]
class ResetPasswordWithCode extends Component
{
    public $step = 1; // 1 = solicitar email, 2 = código + nova senha
    public $email = '';
    public $code = '';
    public $password = '';
    public $password_confirmation = '';
    public $error = '';
    public $success = '';

    public function mount()
    {
        // Se já estiver logado, redireciona
        if (Auth::check()) {
            return redirect()->route('orders.index');
        }
    }

    protected function rules()
    {
        if ($this->step === 1) {
            return ['email' => 'required|email|exists:users,email'];
        }
        
        return [
            'code' => 'required|digits:6',
            'password' => 'required|min:8|confirmed',
        ];
    }

    protected $messages = [
        'email.required' => 'O campo e-mail é obrigatório.',
        'email.email' => 'Digite um e-mail válido.',
        'email.exists' => 'Este e-mail não está cadastrado.',
        'code.required' => 'Digite o código de verificação.',
        'code.digits' => 'O código deve ter 6 dígitos.',
        'password.required' => 'O campo senha é obrigatório.',
        'password.min' => 'A senha deve ter no mínimo 8 caracteres.',
        'password.confirmed' => 'As senhas não coincidem.',
    ];

    public function requestCode()
    {
        $this->validate(['email' => 'required|email|exists:users,email']);
        $this->error = '';

        $user = User::where('email', $this->email)->first();
        
        if ($user) {
            $service = new VerificationCodeService();
            $service->createAndSend($user, VerificationCode::TYPE_PASSWORD_RESET);
            
            $this->success = 'Código enviado para seu email!';
            $this->step = 2;
        } else {
            $this->error = 'Email não encontrado.';
        }
    }

    public function resetPassword()
    {
        $this->validate();
        $this->error = '';

        $user = User::where('email', $this->email)->first();
        
        if (!$user) {
            $this->error = 'Usuário não encontrado.';
            return;
        }

        $service = new VerificationCodeService();
        $result = $service->validate(
            $this->code,
            VerificationCode::TYPE_PASSWORD_RESET,
            $user
        );

        if ($result['valid']) {
            // Atualizar senha
            $user->password = Hash::make($this->password);
            $user->save();

            // Marcar código como usado
            $result['verification_code']->markAsUsed();

            session()->flash('success', 'Senha redefinida com sucesso!');
            return redirect()->route('login');
        } else {
            $this->error = $result['reason'];
        }
    }

    public function back()
    {
        $this->step = 1;
        $this->code = '';
        $this->password = '';
        $this->password_confirmation = '';
        $this->error = '';
        $this->success = '';
    }

    public function render()
    {
        return view('livewire.auth.reset-password-with-code');
    }
}

