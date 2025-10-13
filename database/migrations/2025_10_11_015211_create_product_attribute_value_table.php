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
    Schema::create('product_attribute_value', function (Blueprint $table) {
        // Coluna para o ID do produto
        $table->foreignId('product_id')->constrained()->onDelete('cascade');

        // Coluna para o ID do valor do atributo
        $table->foreignId('attribute_value_id')->constrained()->onDelete('cascade');

        // Define que a combinação das duas colunas acima é a chave primária.
        // Isso impede que o mesmo atributo seja adicionado duas vezes ao mesmo produto.
        $table->primary(['product_id', 'attribute_value_id']);
    });
}
};
