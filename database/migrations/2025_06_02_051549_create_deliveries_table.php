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
        Schema::create('deliveries', function (Blueprint $table) {
            $table->id();
            $table->string('received_by'); // person who received the delivery
            $table->text('notes')->nullable();
        
            // Missing fields
            $table->unsignedBigInteger('product_id');
            $table->integer('quantity');
            $table->decimal('weight_per_item', 8, 2); // adjust precision if needed
            $table->string('type'); // 'In' or 'Out'
        
            $table->timestamps();
        
            // Optional: Add foreign key constraint
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deliveries');
    }
};
