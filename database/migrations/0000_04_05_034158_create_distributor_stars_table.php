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
        Schema::create('distributor_stars', function (Blueprint $table) {
            $table->id();
            $table->string('identification')->nullable();
            $table->string('name')->nullable();
            $table->integer('score')->default(0);
            $table->foreignId('wilaya_id')->nullable()->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('distributor_stars');
    }
};
