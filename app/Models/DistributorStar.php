<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DistributorStar extends Model
{
    use HasFactory;

    public function rls_activationCode()
    {
        return $this->hasMany(ActivationCode::class);
    }
}
