<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('shops', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            
            $table->string('shop_name');
            $table->mediumText('details')->nullable();
            $table->longText('contacts')->nullable();
            $table->mediumText('location')->nullable();
            $table->mediumText('map_location')->nullable();
            $table->integer('nb_followers')->default(0);
            $table->integer('nb_likes')->default(0);
            
            $table->mediumText('threeD_model')->nullable();
            $table->smallInteger('wilaya')->nullable();

            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shops');
    }
};
