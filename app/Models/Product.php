<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    // Supaya bisa diisi massal (Create)
    protected $fillable = [
        'category_id',
        'name',
        'description',
        'price',
        'stock',
        'weight',      // Wajib ada buat RajaOngkir
        'image_path',
    ];

    // Relasi: Setiap Produk pasti punya 1 Kategori
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
