<?php // Arquivo: ..._add_cpf_and_celular_to_users_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Adiciona as colunas depois da coluna 'email'
            $table->string('celular', 20)->nullable()->after('email');
            $table->string('cpf', 14)->nullable()->after('celular');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['celular', 'cpf']);
        });
    }
};