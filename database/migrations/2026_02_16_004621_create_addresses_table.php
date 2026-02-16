<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();

            // PENTING: Alamat ini punya siapa? (Relasi ke User)
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            $table->string('name'); // Nama Penerima (misal: Kantor, Rumah Mertua)
            $table->string('phone'); // No HP Penerima
            $table->text('full_address'); // Jalan, RT/RW, No Rumah

            // Integrasi RajaOngkir
            $table->integer('province_id'); // ID Provinsi dari RajaOngkir
            $table->integer('city_id');     // ID Kota dari RajaOngkir
            $table->string('postal_code');

            $table->boolean('is_primary')->default(false); // Alamat Utama?

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
