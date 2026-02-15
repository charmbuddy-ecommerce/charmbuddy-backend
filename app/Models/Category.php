<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
<<<<<<< HEAD
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['parent_id', 'name', 'image'];

    // Relasi: Siapa Bapak saya?
    public function parent()
=======
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = [
        'name',
        'parent_id',
    ];

    public function parent(): BelongsTo
>>>>>>> 6e1cce851ef20e31b37d59882f762c1f19b33086
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

<<<<<<< HEAD
    // Relasi: Siapa Anak-anak saya?
    public function children()
=======
    public function children(): HasMany
>>>>>>> 6e1cce851ef20e31b37d59882f762c1f19b33086
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

<<<<<<< HEAD
    // Relasi: Produk apa aja yang ada di kategori ini?
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
=======
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}

>>>>>>> 6e1cce851ef20e31b37d59882f762c1f19b33086
