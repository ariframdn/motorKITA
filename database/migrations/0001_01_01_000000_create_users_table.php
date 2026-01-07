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
        // 1. TABEL USERS (Diperbarui dengan Role, Phone, Address)
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            
            // --- TAMBAHAN KHUSUS MOTORKITA ---
            // Role: admin, mechanic (mekanik), customer (pelanggan)
            $table->enum('role', ['admin', 'mechanic', 'customer'])->default('customer');
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            // ---------------------------------

            $table->rememberToken();
            $table->timestamps();
        });

        // Default Laravel (Jangan dihapus)
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        // Default Laravel (Jangan dihapus)
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });

        // --- MULAI TABEL PROYEK MOTORKITA ---

        // 2. TABEL MOTORCYCLES (Motor milik Customer)
        Schema::create('motorcycles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('brand'); // Merk: Honda, Yamaha
            $table->string('plate_number'); // Plat: D 1234 ABC
            $table->string('year')->nullable(); // Tahun motor
            $table->string('color')->nullable();
            $table->timestamps();
        });

        // 3. TABEL SERVICE CATALOGS (Daftar Jasa Servis)
        Schema::create('service_catalogs', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nama Jasa: Ganti Oli, Tune Up
            $table->decimal('price', 10, 2); // Harga Jasa
            $table->string('description')->nullable();
            $table->integer('duration')->nullable(); // Estimasi waktu (menit)
            $table->timestamps();
        });

        // 4. TABEL SPARE PARTS (Suku Cadang)
        Schema::create('spare_parts', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nama Barang: Kampas Rem
            $table->string('code')->unique()->nullable(); // Kode Barang
            $table->integer('stock'); // Stok
            $table->decimal('price', 10, 2); // Harga Barang
            $table->string('brand')->nullable(); // Merk Sparepart
            $table->timestamps();
        });

        // 5. TABEL BOOKINGS (Transaksi Utama)
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users'); // Pemilik Motor (Customer)
            $table->foreignId('mechanic_id')->nullable()->constrained('users'); // Mekanik yang menangani
            $table->dateTime('booking_date'); // Tanggal & Jam Servis
            // Status Pengerjaan
            $table->enum('status', ['pending', 'confirmed', 'processing', 'completed', 'canceled'])->default('pending');
            $table->decimal('total_price', 12, 2)->default(0);
            $table->text('notes')->nullable(); // Keluhan Customer
            $table->timestamps();
        });

        // 6. TABEL BOOKING DETAILS (Pivot: Detail Jasa & Sparepart per Booking)
        Schema::create('booking_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->onDelete('cascade');
            
            // Bisa isi Jasa saja, atau Sparepart saja (nullable)
            $table->foreignId('service_catalog_id')->nullable()->constrained('service_catalogs');
            $table->foreignId('spare_part_id')->nullable()->constrained('spare_parts');
            
            $table->integer('qty')->default(1);
            $table->decimal('price', 10, 2); // Harga saat transaksi (takut harga master berubah)
            $table->decimal('subtotal', 10, 2);
            $table->timestamps();
        });

        // 7. TABEL PAYMENTS (Pembayaran)
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->onDelete('cascade');
            $table->decimal('amount', 12, 2);
            $table->string('payment_method')->nullable(); // Cash, Transfer
            $table->string('status')->default('pending'); // pending, paid
            $table->string('proof_image')->nullable(); // Bukti Transfer (File Upload)
            $table->timestamp('payment_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Hapus tabel urut dari anak ke induk (biar tidak error foreign key)
        Schema::dropIfExists('payments');
        Schema::dropIfExists('booking_details');
        Schema::dropIfExists('bookings');
        Schema::dropIfExists('spare_parts');
        Schema::dropIfExists('service_catalogs');
        Schema::dropIfExists('motorcycles');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};