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
        Schema::table('users', function (Blueprint $table) {
            // Adiciona a coluna 'is_admin' do tipo booleano (true/false).
            // Por padrão, todo novo usuário terá o valor 'false'.
            $table->boolean('is_admin')->default(false)->after('remember_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Este método é usado se precisarmos desfazer a migration.
            // Ele simplesmente remove a coluna.
            $table->dropColumn('is_admin');
        });
    }
};