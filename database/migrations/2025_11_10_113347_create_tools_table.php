<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tools', function (Blueprint $table) {
            $table->bigIncrements('tool_id');

            $table->unsignedBigInteger('organization_id');
            $table->unsignedBigInteger('warehouse_id');

            $table->string('jenis');
            $table->string('nama');
            $table->string('merk')->nullable();
            $table->text('deskripsi')->nullable();

            $table->integer('jumlah');
            $table->string('satuan', 100);

            $table->date('tanggal_pengadaan')->nullable();
            $table->date('tanggal_kadaluarsa')->nullable();

            $table->timestamps();

            $table->foreign('organization_id')->references('organization_id')->on('organizations')->onDelete('cascade');
            $table->foreign('warehouse_id')->references('warehouse_id')->on('warehouses')->onDelete('cascade');

            $table->index('nama');
            $table->index('jenis');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tools');
    }
};
