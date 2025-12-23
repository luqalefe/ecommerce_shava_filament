<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('verification_codes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('code', 6); // Código de 6 dígitos
            $table->string('type'); // 'email_verification' ou 'password_reset'
            $table->timestamp('expires_at'); // Expira em 15 minutos
            $table->boolean('used')->default(false); // Marca se foi usado
            $table->timestamps();
            
            // Índices para busca rápida
            $table->index(['code', 'type', 'used']);
            $table->index('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('verification_codes');
    }
};
