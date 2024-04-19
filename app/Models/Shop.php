<?php

namespace App\Models;

use App\Models\Item;
use App\Models\PaymentHistory;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Shop extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'activation_code_id',
        'username',
        'phone_number',
        'password',
        'password_plainText',
        'shop_name',
        'shop_image',
        'bio',
        'contacts',
        'wilaya_id',
        'map_location',
        'nb_followers',
        'nb_likes',
        'wilaya_created_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'phone_number_verified_at' => 'datetime',
    ];

    public function rls_user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function rls_items(): HasMany
    {
        return $this->hasMany(Item::class);
    }

    /*
    public function paymentTransactions(): HasMany
    {
        return $this->hasMany(PaymentTransaction::class);
    }
    
    public function creditTransactions(): HasMany
    {
        return $this->hasMany(CreditTransaction::class);
    }
    */

    public function rls_followers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_shop_followings', 'shop_id', 'user_id');
    }

    public function rls_followedByUser(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_shop_followings', 'shop_id', 'user_id');
    }

    public function rls_collections()
    {
        return $this->belongsToMany(Collection::class, 'shop_collections')
                    ->withPivot('nb_new');
    }

}
