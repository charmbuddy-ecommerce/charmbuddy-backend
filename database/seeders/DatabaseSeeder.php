<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Product; // <--- JANGAN LUPA INI
use App\Models\Category; // <--- DAN INI
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat User Admin (Yang tadi sudah ada)
        User::factory()->create([
            'name' => 'Admin Bafqi',
            'email' => 'admin@charmbuddy.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        // 2. Buat Kategori Dulu
        $kategori = Category::create([
            'name' => 'Charm Lucu',
            'slug' => 'charm-lucu',
        ]);

        // 3. Buat Produk
        Product::create([
            'name' => 'Charm Bintang',
            'slug' => 'charm-bintang',
            'description' => 'Charm berbentuk bintang yang bersinar',
            'price' => 15000,
            'stock' => 100,
            'category_id' => $kategori->id, // Sambungkan ke ID kategori tadi
            // 'image' => 'path/to/image.jpg' (kalau ada kolom image)
        ]);

          // Buat User Biasa (Opsional)
        \App\Models\User::factory()->create([
        'name' => 'Customer Test',
        'email' => 'customer@charmbuddy.com',
        'password' => bcrypt('password'),
        'role' => 'customer',
         ]);
    }
}
