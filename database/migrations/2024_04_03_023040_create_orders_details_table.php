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
        Schema::create('order_details', function (Blueprint $table) {
            $table->id();
            $table->string('delivery_details');
            $table->integer('qty');
            $table->decimal('weight');
            $table->decimal('protection_fee');
            $table->decimal('shipping_fee');
            $table->decimal('price');
            $table->integer('origin');
            $table->integer('destination');
            $table->string('courier');
            $table->unsignedBigInteger('product_id');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade'); // Tambahkan onDelete('cascade')
           
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_details');
    }
};
