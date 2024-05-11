<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivationCode extends Model
{
    use HasFactory;

    public static function generate($max_length = 6): string
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        $codeLength = rand(1, $max_length); // Generate a code between 1 and $max_length characters long

        $code = '';
        for ($i = 0; $i < $codeLength; $i++) {
            $code .= $characters[random_int(0, strlen($characters) - 1)];
        }

        return $code;
    }

    public function rls_distributor()
    {
        return $this->belongsTo(DistributorStar::class, 'distributor_star_id');
    }

}
