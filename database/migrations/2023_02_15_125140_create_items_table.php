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
            $table->foreignId('user_id')->nullable()->constrained();
            $table->foreignId('wilaya_id')->nullable()->constrained();
            $table->foreignId('product_type_id')->nullable()->constrained();

            $table->mediumText('details')->nullable();
            $table->string('name');
            $table->mediumText('sizes')->nullable();
            $table->integer('stock')->default(1);
            $table->string('price')->nullable();
            $table->string('product_type')->nullable();

            $table->string('genders')->nullable(); //_male, _female, _male/_female

            $table->longText('images')->nullable();

            $table->longText('keywords')->nullable();
            
            $table->integer('isActive')->default(1);

            $table->dateTime('last_reposted')->nullable();
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
