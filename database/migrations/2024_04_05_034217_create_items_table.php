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
            $table->foreignId('item_type_id')->nullable()->constrained();

            $table->string('name');
            $table->mediumText('details')->nullable();
            $table->string('price')->nullable();

            $table->string('genders')->nullable(); //male, female, male,female
            $table->json('images')->nullable();
            $table->longText('keywords')->nullable();

            $table->integer('isActive')->default(1);
            $table->dateTime('last_reposted')->nullable();      //fill it with created_at also
            $table->string('wilaya_code')->nullable();
            
            $table->integer('nb_reports')->default(0);
            
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
