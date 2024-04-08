<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'reasons'];

    public function rls_user()
    {
        return $this->belongsTo(User::class);
    }

    public function rls_item()
    {
        return $this->belongsTo(Item::class);
    }

}
