<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->string('session_id')->nullable(); // For guest users
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade'); // For authenticated users
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->integer('quantity');
            $table->json('product_options')->nullable(); // For variants, etc.
            $table->timestamps();
            
            $table->index(['session_id']);
            $table->index(['user_id']);
            $table->unique(['session_id', 'product_id', 'product_options'], 'cart_session_product_unique');
            $table->unique(['user_id', 'product_id', 'product_options'], 'cart_user_product_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};