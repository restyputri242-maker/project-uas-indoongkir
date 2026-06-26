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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('status')->default('belum_bayar'); // belum_bayar, dikirim, selesai
            $table->integer('weight'); // total weight in grams
            $table->integer('subtotal');
            $table->integer('shipping_cost');
            $table->integer('total');
            $table->string('courier');
            $table->string('service');
            $table->string('province');
            $table->string('city');
            $table->text('address_details');
            $table->string('tracking_number')->nullable();
            $table->timestamps();
        });

        Schema::create('transaction_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->nullable()->constrained()->onDelete('set null');
            $table->string('product_name'); // to preserve product name if deleted
            $table->integer('price'); // snapshot of price
            $table->integer('quantity');
            $table->integer('weight'); // snapshot of weight
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_items');
        Schema::dropIfExists('transactions');
    }
};