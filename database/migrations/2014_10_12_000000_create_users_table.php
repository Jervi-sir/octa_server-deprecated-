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
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            $table->string('phone_number')->unique();       //for login
            $table->timestamp('phone_number_verified_at')->nullable();
            $table->string('password');
            $table->string('password_plainText');

            $table->string('name')->nullable();
            $table->string('username')->nullable()->unique();
            $table->mediumText('bio')->default('Octa User');

            $table->longText('profile_images')->nullable();
            $table->longText('contacts')->nullable();
            $table->integer('nb_likes')->default(0);
            $table->integer('nb_friends')->default(0);
            $table->boolean('isPremium')->default(0);
            $table->integer('credit')->default(0);

            $table->longText('collections')->nullable();        //might take it off
            $table->string('wilaya_code')->nullable();        //might take it off
            $table->string('wilaya_name')->nullable();        //might take it off

            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
