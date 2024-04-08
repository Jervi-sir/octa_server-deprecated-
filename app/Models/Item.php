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

    public function rls_store(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    public function rls_wilaya(): BelongsTo
    {
        return $this->belongsTo(Wilaya::class);
    }

    public function rls_savedByUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_saves', 'item_id', 'user_id');
    }

    public function rls_messages()
    {
        return $this->hasMany(Message::class);
    }
    
    public function rls_reports()
    {
        return $this->hasMany(Report::class);
    }
    
}
