<?php
/**
 * Script para criar usuário admin diretamente
 * Execute: php criar_admin.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

try {
    // Verificar se o usuário já existe
    $existingUser = User::where('email', 'luqalefe@gmail.com')->first();
    
    if ($existingUser) {
        echo "❌ Usuário já existe! Atualizando...\n";
        $existingUser->update([
            'name' => 'Lucas Admin',
            'password' => Hash::make('12345678'),
            'is_admin' => true,
            'role' => 'admin',
        ]);
        echo "✅ Usuário atualizado com sucesso!\n";
    } else {
        // Criar novo usuário
        $user = User::create([
            'name' => 'Lucas Admin',
            'email' => 'luqalefe@gmail.com',
            'password' => Hash::make('12345678'),
            'is_admin' => true,
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);
        
        echo "✅ Usuário admin criado com sucesso!\n";
    }
    
    echo "\n📋 Credenciais:\n";
    echo "   Email: luqalefe@gmail.com\n";
    echo "   Senha: 12345678\n";
    echo "\n⚠️  IMPORTANTE: Altere a senha após fazer login!\n";
    
} catch (\Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
    exit(1);
}

