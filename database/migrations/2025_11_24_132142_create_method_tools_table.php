<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('method_tools', function (Blueprint $table) {
            $table->id('method_id');
            $table->foreignId('organization_id')->index();
            $table->string('nama_method');
            $table->json('list_tools');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('method_tools');
    }
};
