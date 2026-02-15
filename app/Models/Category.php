<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['parent_id', 'name', 'image'];

    // Relasi: Siapa Bapak saya?
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    // Relasi: Siapa Anak-anak saya?
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    // Relasi: Produk apa aja yang ada di kategori ini?
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
