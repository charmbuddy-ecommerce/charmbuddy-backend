<?php

namespace App\Models;

<<<<<<< HEAD
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    // Supaya bisa diisi massal (Create)
=======
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
>>>>>>> 6e1cce851ef20e31b37d59882f762c1f19b33086
    protected $fillable = [
        'category_id',
        'name',
        'description',
        'price',
        'stock',
<<<<<<< HEAD
        'weight',      // Wajib ada buat RajaOngkir
        'image_path',
    ];

    // Relasi: Setiap Produk pasti punya 1 Kategori
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
=======
        'weight',
        'image_path',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }
}

>>>>>>> 6e1cce851ef20e31b37d59882f762c1f19b33086
