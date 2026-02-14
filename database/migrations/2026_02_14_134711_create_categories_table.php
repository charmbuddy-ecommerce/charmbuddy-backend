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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            
            // --- INI LOGIKA PARENT_ID (SELF REFERENCE) ---
            // 1. Boleh Kosong (nullable) -> Karena kategori induk (Top Level) gak punya bapak.
            // 2. constrained('categories') -> Nunjuk ke tabel dirinya sendiri.
            // 3. nullOnDelete() -> Kalau Induk dihapus, anaknya jadi yatim (bukan ikut terhapus).
            $table->foreignId('parent_id')->nullable()->constrained('categories')->nullOnDelete();
            
            $table->string('name');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
