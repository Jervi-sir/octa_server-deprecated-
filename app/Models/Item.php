<?php

namespace App\Models;

use App\Models\Shop;
use App\Models\User;
use App\Models\Wilaya;
use App\Models\ItemImage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Item extends Model
{
    use HasFactory;

    protected $fillable = ['*'];
    protected $hidden = [
        'user_id'
    ];

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    public function backUpImages(): HasMany
    {
        return $this->hasMany(ItemImage::class);
    }

    public function wilaya(): BelongsTo
    {
        return $this->belongsTo(Wilaya::class);
    }

    public function savedByUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_saves', 'item_id', 'user_id');
    }

    public function chats()
    {
        return $this->belongsToMany(Chat::class, 'chat_items')
                    ->withTimestamps();
    }

}
