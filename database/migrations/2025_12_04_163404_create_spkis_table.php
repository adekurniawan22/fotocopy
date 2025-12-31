<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('spki', function (Blueprint $table) {
            $table->id('spki_id');
            
            $table->unsignedBigInteger('organization_id');
            $table->foreign('organization_id')->references('organization_id')->on('organizations')->onDelete('cascade');

            $table->string('nomor_spki', 100)->unique();
            
            $table->string('dari');
            
            $table->unsignedBigInteger('kepada'); 
            $table->foreign('kepada')->references('user_id')->on('users')->onDelete('cascade');

            $table->string('macam_pekerjaan');
            $table->string('lokasi_pekerjaan');
            $table->date('mulai_pelaksanaan');
            $table->date('selesai_pelaksanaan');

            $table->unsignedBigInteger('penanggung_jawab_id')->nullable();
            $table->string('penanggung_jawab_nama');
            $table->foreign('penanggung_jawab_id')->references('user_id')->on('users')->nullOnDelete();

            $table->unsignedBigInteger('pengawas_pekerjaan_id')->nullable();
            $table->string('pengawas_pekerjaan_nama');
            $table->foreign('pengawas_pekerjaan_id')->references('user_id')->on('users')->nullOnDelete();

            $table->unsignedBigInteger('pengawas_k3_id')->nullable();
            $table->string('pengawas_k3_nama');
            $table->foreign('pengawas_k3_id')->references('user_id')->on('users')->nullOnDelete();

            $table->json('pelaksana')->nullable(); 
            $table->json('peralatan')->nullable();

            $table->text('kendaraan')->nullable();
            $table->text('uraian_pekerjaan');
            $table->text('catatan')->nullable();
            $table->text('revision_note')->nullable();

            $table->enum('status', ['DRAFT', 'APPROVED', 'REVISION'])->default('DRAFT');
            
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->dateTime('approved_at')->nullable();
            $table->foreign('approved_by')->references('user_id')->on('users')->nullOnDelete();

            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')->references('user_id')->on('users')->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('spki');
    }
};