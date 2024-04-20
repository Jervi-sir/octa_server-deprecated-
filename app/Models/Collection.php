<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Collection extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'name', 'thumbnail'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function rls_shops()
    {
        //return $this->belongsToMany(Shop::class, 'shop_collections');
        return $this->belongsToMany(Shop::class, 'shop_collections')
                    ->withPivot('nb_new')
                    ->withTimestamps();
    }
}
