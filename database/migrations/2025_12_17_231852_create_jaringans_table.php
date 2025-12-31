<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('jaringan', function (Blueprint $table) {
            $table->id('jaringan_id');
            $table->unsignedBigInteger('organization_id')->index();
            
            $table->string('bay_line');
            $table->text('keterangan')->nullable();
            $table->string('foto')->nullable();
            
            $table->json('riwayat')->nullable(); 

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('jaringan');
    }
};