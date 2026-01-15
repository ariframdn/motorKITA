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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->onDelete('cascade');
            $table->foreignId('customer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('mechanic_id')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('review_type', ['workshop', 'mechanic', 'both'])->default('both');
            $table->integer('rating_workshop')->nullable(); // 1-5 stars
            $table->integer('rating_mechanic')->nullable(); // 1-5 stars
            $table->text('comment')->nullable();
            $table->string('photo')->nullable();
            $table->timestamps();
            
            $table->index(['mechanic_id', 'rating_mechanic']);
            $table->index(['booking_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
