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
            $table->string('phone_number')->unique();
            $table->timestamp('phone_number_verified_at')->nullable();
            $table->string('password');
            $table->string('password_plainText');

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

            //$table->string('email')->unique();
            //$table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
