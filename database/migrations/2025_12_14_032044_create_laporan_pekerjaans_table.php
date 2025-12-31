<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laporan_pekerjaan', function (Blueprint $table) {
            $table->id('laporan_pekerjaan_id');

            $table->unsignedBigInteger('organization_id');

            $table->string('judul_laporan');
            $table->text('dasar_pelaksanaan');
            $table->date('mulai_pelaksanaan');
            $table->date('selesai_pelaksanaan');
            $table->text('lingkup_pekerjaan');
            $table->text('hasil_pekerjaan');
            $table->json('lampiran');
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->enum('status', ['DRAFT', 'REVISION', 'APPROVED'])->default('DRAFT');

            $table->text('revision_note')->nullable();

            $table->timestamps();

            $table->foreign('organization_id')
                ->references('organization_id')
                ->on('organizations')
                ->onDelete('cascade');

            $table->foreign('created_by')->references('user_id')->on('users')->onDelete('cascade');
            $table->foreign('approved_by')->references('user_id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan_pekerjaan');
    }
};
