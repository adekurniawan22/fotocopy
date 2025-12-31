<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('gardu_induk', function (Blueprint $table) {
            $table->id('gardu_induk_id');
            $table->unsignedBigInteger('organization_id')->index();
            
            $table->string('gardu_induk');
            $table->text('keterangan')->nullable();
            $table->string('foto')->nullable();
            
            $table->json('riwayat')->nullable(); 

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('gardu_induk');
    }
};