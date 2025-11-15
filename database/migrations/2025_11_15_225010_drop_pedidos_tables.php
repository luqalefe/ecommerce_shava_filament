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
        // Remove a tabela pedido_produto primeiro (tem foreign key para pedidos)
        Schema::dropIfExists('pedido_produto');
        
        // Remove a tabela pedidos
        Schema::dropIfExists('pedidos');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Se precisar reverter, você pode recriar as tabelas aqui
        // Mas como são redundantes, deixamos vazio
    }
};