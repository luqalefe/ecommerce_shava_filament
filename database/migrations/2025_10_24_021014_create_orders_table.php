<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // Verifique se sua tabela de endereços se chama 'enderecos'
            $table->foreignId('endereco_id')->constrained('enderecos')->onDelete('restrict');

            $table->string('status')->default('pending'); // Ex: pending, paid, cancelled
            $table->decimal('total_amount', 10, 2);
            $table->string('payment_method');
            $table->string('payment_id')->nullable(); // ID de pagamento da Abacate Pay
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }

};
