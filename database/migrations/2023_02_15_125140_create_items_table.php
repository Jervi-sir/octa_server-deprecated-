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
        Schema::create('items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('shop_id')->constrained();
            $table->string('shop_name');
            $table->longText('shop_image');

            $table->mediumText('details');
            $table->string('contacts');
            $table->mediumText('map_location');
            
            $table->string('name');
            $table->longText('item_images');
            $table->mediumText('size');
            $table->integer('stock');
            $table->string('price');
            $table->string('type');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
