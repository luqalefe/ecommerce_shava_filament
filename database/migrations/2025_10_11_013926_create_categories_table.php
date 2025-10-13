<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up(): void
{
    Schema::create('categories', function (Blueprint $table) {
        $table->id();
        // Adicionamos esta linha
        $table->foreignId('parent_id')->nullable()->constrained('categories')->onDelete('cascade');
        $table->string('name');
        $table->string('slug')->unique();
        $table->timestamps();
    });
}

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};