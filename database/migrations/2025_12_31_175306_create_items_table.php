<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id('item_id');
            $table->string('item_name');
            $table->json('foto');

            $table->bigInteger('buy_price');
            $table->bigInteger('sell_price');

            $table->string('unit');
            $table->string('letak')->nullable();
            $table->string('location')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
