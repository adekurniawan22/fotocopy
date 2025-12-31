<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWorkPlansTable extends Migration
{
    public function up()
    {
        Schema::create('work_plans', function (Blueprint $table) {
            $table->id('work_plan_id');
            $table->unsignedBigInteger('organization_id');
            $table->string('work_plan_name');

            $table->json('list_items');

            $table->date('date_start');
            $table->date('date_finish');
            $table->boolean('is_done')->default(false);
            $table->timestamps();

            $table->foreign('organization_id')
                ->references('organization_id')
                ->on('organizations')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('work_plans');
    }
}
