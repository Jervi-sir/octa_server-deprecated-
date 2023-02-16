<?php

namespace App\Models;

use App\Models\Shop;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'shop_name',
        'shop_image',
        'details',
        'contacts',
        'map_location',
        'name',
        'item_images',
        'size',
        'stock',
        'type',
        'price',
    ];

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

}
