<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('history_tools', function (Blueprint $table) {
            $table->id('history_tool_id');
            $table->foreignId('organization_id')->index();
            $table->enum('type', ['Internal', 'Eksternal']);
            
            $table->enum('status', ['draft', 'approved'])->default('draft'); 

            $table->foreignId('created_by'); 
            
            $table->json('list_tools'); 
            $table->json('list_user'); 
            
            $table->date('exit_date');
            
            $table->boolean('is_returned')->default(0); 
            $table->date('return_date')->nullable(); 

            $table->text('keterangan')->nullable();
            $table->string('foto')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('history_tools');
    }
};