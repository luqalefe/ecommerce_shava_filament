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
            // Adicionar campos de peso e dimensões para integração com Frenet API
            // Peso em quilogramas (kg)
            $table->decimal('weight', 8, 2)->default(0.5)->after('quantity')
                ->comment('Peso do produto em kg');
            
            // Dimensões em centímetros (cm)
            $table->decimal('height', 8, 2)->default(10)->after('weight')
                ->comment('Altura da embalagem em cm');
            
            $table->decimal('width', 8, 2)->default(10)->after('height')
                ->comment('Largura da embalagem em cm');
            
            $table->decimal('length', 8, 2)->default(10)->after('width')
                ->comment('Comprimento da embalagem em cm');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Remover os campos na ordem reversa
            $table->dropColumn(['length', 'width', 'height', 'weight']);
        });
    }
};
