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
            $table->string('username')->unique();     //for login
            $table->string('phone_number');
            $table->timestamp('phone_number_verified_at')->nullable();
            $table->string('password');
            $table->string('password_plainText');
            
            $table->string('shop_name');
            $table->string('shop_image')->nullable();
            $table->mediumText('bio')->nullable();
            $table->json('contacts')->nullable();

            $table->foreignId('wilaya_id')->constrained();
            $table->mediumText('map_location')->nullable();

            $table->integer('nb_followers')->default(0);
            $table->integer('nb_likes')->default(0);

            $table->foreign('wilaya_created_at')->references('id')->on('wilayas');

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
