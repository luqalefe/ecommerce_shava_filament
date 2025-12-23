<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use App\Services\VerificationCodeService;
use App\Models\VerificationCode;
use Illuminate\Support\Facades\Auth;

class VerifyEmailCode extends Component
{
    public $code = '';
    public $error = '';
    public $success = '';
    public $resendCooldown = 0;

    protected $rules = [
        'code' => 'required|digits:6',
    ];

    public function mount()
    {
        // Redirecionar se já verificado
        if (Auth::check() && Auth::user()->hasVerifiedEmail()) {
            return redirect()->route('home');
        }

        // Redirecionar se não logado
        if (!Auth::check()) {
            return redirect()->route('login');
        }
    }

    public function verifyCode()
    {
        $this->validate();
        $this->error = '';
        $this->success = '';

        $service = new VerificationCodeService();
        $result = $service->validate(
            $this->code,
            VerificationCode::TYPE_EMAIL_VERIFICATION,
            Auth::user()
        );

        if ($result['valid']) {
            // Marcar código como usado
            $result['verification_code']->markAsUsed();

            // Marcar email como verificado
            Auth::user()->markEmailAsVerified();

            $this->success = 'Email verificado com sucesso!';

            // Redirecionar após 2 segundos
            $this->dispatch('email-verified');
            
            session()->flash('success', 'Email verificado com sucesso!');
            return redirect()->route('home');
        } else {
            $this->error = $result['reason'];
        }
    }

    public function resendCode()
    {
        if ($this->resendCooldown > 0) {
            $this->error = "Aguarde {$this->resendCooldown} segundos para reenviar.";
            return;
        }

        $service = new VerificationCodeService();
        $service->createAndSend(Auth::user(), VerificationCode::TYPE_EMAIL_VERIFICATION);

        $this->success = 'Código reenviado! Verifique seu email.';
        $this->resendCooldown = 60; // 60 segundos de cooldown
    }

    public function decrementCooldown()
    {
        if ($this->resendCooldown > 0) {
            $this->resendCooldown--;
        }
    }

    public function render()
    {
        return view('livewire.auth.verify-email-code')
            ->layout('layouts.guest');
    }
}
