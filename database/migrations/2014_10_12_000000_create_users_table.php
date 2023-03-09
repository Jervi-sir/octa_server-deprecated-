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
            
            $table->foreignId('role_id')->constrained();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');

            $table->string('name')->nullable();
            $table->string('username')->nullable();
            $table->mediumText('bio')->default('Octa User');

            $table->longText('profile_images')->nullable();
            $table->longText('contacts')->nullable();
            $table->integer('nb_likes')->default(0);
            $table->integer('nb_followers')->default(0);
            $table->boolean('isPremium')->default(0);

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
