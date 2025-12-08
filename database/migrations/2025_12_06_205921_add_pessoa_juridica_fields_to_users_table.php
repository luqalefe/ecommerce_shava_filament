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
            // Tipo de pessoa: 'pf' (pessoa física) ou 'pj' (pessoa jurídica)
            $table->enum('user_type', ['pf', 'pj'])
                ->default('pf')
                ->after('cpf')
                ->comment('Tipo de usuário: PF (Pessoa Física) ou PJ (Pessoa Jurídica)');
            
            // Campos específicos para Pessoa Jurídica
            $table->string('cnpj', 18)->nullable()->after('user_type')
                ->comment('CNPJ para Pessoa Jurídica (com máscara)');
            
            $table->string('razao_social', 255)->nullable()->after('cnpj')
                ->comment('Razão Social da empresa (nome legal)');
            
            $table->string('nome_fantasia', 255)->nullable()->after('razao_social')
                ->comment('Nome Fantasia da empresa (opcional)');
            
            $table->string('inscricao_estadual', 20)->nullable()->after('nome_fantasia')
                ->comment('Inscrição Estadual da empresa (opcional)');
        });
        
        // Criar índices para melhor performance em queries
        Schema::table('users', function (Blueprint $table) {
            $table->index('user_type');
            $table->index('cnpj');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Remover índices primeiro
            $table->dropIndex(['user_type']);
            $table->dropIndex(['cnpj']);
            
            // Remover colunas
            $table->dropColumn([
                'user_type',
                'cnpj',
                'razao_social',
                'nome_fantasia',
                'inscricao_estadual'
            ]);
        });
    }
};
