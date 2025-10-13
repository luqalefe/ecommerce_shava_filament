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
        Schema::table('products', function (Blueprint $table) {
            // Adiciona a coluna 'quantity' do tipo inteiro, com valor padrão 0.
            // O ->after('is_active') posiciona a coluna no banco de dados para melhor organização.
            $table->integer('quantity')->default(0)->after('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Se precisarmos reverter, este método remove a coluna.
            $table->dropColumn('quantity');
        });
    }
};