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
        Schema::table('bookings', function (Blueprint $table) {
            $table->foreignId('promo_code_id')->nullable()->constrained('promo_codes')->onDelete('set null')->after('cost');
            $table->decimal('discount_amount', 10, 2)->default(0)->after('promo_code_id');
            $table->decimal('hpp_cost', 10, 2)->default(0)->after('discount_amount'); // Cost of Goods Sold
            $table->decimal('final_cost', 10, 2)->default(0)->after('hpp_cost'); // Cost after discount
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['promo_code_id']);
            $table->dropColumn(['promo_code_id', 'discount_amount', 'hpp_cost', 'final_cost']);
        });
    }
};
