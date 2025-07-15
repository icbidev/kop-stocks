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
            $table->foreignId('weight_unit_id')->nullable()->constrained('weight_units');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // First drop the foreign key
            $table->dropForeign(['weight_unit_id']);
            
            // Then drop the column itself
            $table->dropColumn('weight_unit_id');
        });
    }
};
