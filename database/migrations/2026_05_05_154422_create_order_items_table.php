<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('order_items', function (Blueprint $table) {
        $table->id();
        $table->foreignId('order_id')->constrained('orders')->onDelete('cascade'); // Relasi ke tabel orders
        $table->foreignId('product_id')->constrained('products')->onDelete('cascade'); // Relasi ke tabel products
        $table->integer('quantity'); // Jumlah barang yang dibeli
        $table->integer('price'); // Harga satuan saat itu
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
