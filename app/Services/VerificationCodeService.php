<?php

namespace App\Services;

use App\Models\User;
use App\Models\VerificationCode;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class VerificationCodeService
{
    /**
     * Gera um código de 6 dígitos aleatório
     */
    private function generateCode(): string
    {
        return str_pad((string) rand(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Cria e envia um código de verificação para o usuário
     *
     * @param User $user
     * @param string $type VerificationCode::TYPE_EMAIL_VERIFICATION ou TYPE_PASSWORD_RESET
     * @return VerificationCode
     */
    public function createAndSend(User $user, string $type): VerificationCode
    {
        // Invalidar códigos anteriores do mesmo tipo para este usuário
        VerificationCode::where('user_id', $user->id)
            ->where('type', $type)
            ->where('used', false)
            ->update(['used' => true]);

        // Gerar novo código
        $code = $this->generateCode();
        $expiresAt = Carbon::now()->addMinutes(15);

        // Criar registro no banco
        $verificationCode = VerificationCode::create([
            'user_id' => $user->id,
            'code' => $code,
            'type' => $type,
            'expires_at' => $expiresAt,
        ]);

        // Enviar email
        $this->sendEmail($user, $verificationCode);

        Log::info('Código de verificação criado', [
            'user_id' => $user->id,
            'type' => $type,
            'code' => $code, // Em produção, considere não logar o código
            'expires_at' => $expiresAt,
        ]);

        return $verificationCode;
    }

    /**
     * Valida um código fornecido pelo usuário
     *
     * @param string $code
     * @param string $type
     * @param User|null $user Se fornecido, verifica se o código pertence a este usuário
     * @return array ['valid' => bool, 'verification_code' => VerificationCode|null, 'reason' => string|null]
     */
    public function validate(string $code, string $type, ?User $user = null): array
    {
        $query = VerificationCode::where('code', $code)
            ->where('type', $type)
            ->valid();

        if ($user) {
            $query->where('user_id', $user->id);
        }

        $verificationCode = $query->first();

        if (!$verificationCode) {
            return [
                'valid' => false,
                'verification_code' => null,
                'reason' => 'Código inválido ou expirado',
            ];
        }

        return [
            'valid' => true,
            'verification_code' => $verificationCode,
            'reason' => null,
        ];
    }

    /**
     * Envia email com o código
     */
    private function sendEmail(User $user, VerificationCode $verificationCode): void
    {
        $subject = $verificationCode->type === VerificationCode::TYPE_EMAIL_VERIFICATION
            ? 'Verificação de Email - Shava Haux'
            : 'Recuperação de Senha - Shava Haux';

        $message = $this->getEmailMessage($verificationCode);

        Mail::raw($message, function ($m) use ($user, $subject) {
            $m->to($user->email, $user->name)
              ->subject($subject);
        });

        Log::info('Email de verificação enviado', [
            'user_id' => $user->id,
            'email' => $user->email,
            'type' => $verificationCode->type,
        ]);
    }

    /**
     * Monta a mensagem do email
     */
    private function getEmailMessage(VerificationCode $verificationCode): string
    {
        $minutesValid = 15;
        
        if ($verificationCode->type === VerificationCode::TYPE_EMAIL_VERIFICATION) {
            return "Olá!\n\n"
                . "Obrigado por se cadastrar na Shava Haux.\n\n"
                . "Seu código de verificação é:\n\n"
                . "**{$verificationCode->code}**\n\n"
                . "Este código é válido por {$minutesValid} minutos.\n\n"
                . "Se você não solicitou este código, ignore este email.\n\n"
                . "Atenciosamente,\n"
                . "Equipe Shava Haux";
        }

        return "Olá!\n\n"
            . "Você solicitou a recuperação de senha da sua conta Shava Haux.\n\n"
            . "Seu código de verificação é:\n\n"
            . "**{$verificationCode->code}**\n\n"
            . "Este código é válido por {$minutesValid} minutos.\n\n"
            . "Se você não solicitou esta recuperação, ignore este email e sua senha permanecerá inalterada.\n\n"
            . "Atenciosamente,\n"
            . "Equipe Shava Haux";
    }

    /**
     * Remove códigos expirados do banco (garbage collection)
     */
    public function cleanupExpiredCodes(): int
    {
        $count = VerificationCode::where('expires_at', '<', Carbon::now())
            ->orWhere('used', true)
            ->delete();

        Log::info("Códigos expirados removidos: {$count}");

        return $count;
    }
}
