<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'phone',
        'full_address',
        'province_id',
        'city_id',
        'postal_code',
        'is_primary',
    ];

    // Relasi: Alamat ini milik User siapa?
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}