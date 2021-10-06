<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDataChildTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('data_child', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('data_user_id');
            $table->string('name');
            $table->date('dob')->nullable()->default(null);
            $table->string('grade')->nullable()->default(null);
            $table->string('gender')->nullable()->default(null);
            $table->string('class_id')->nullable()->default(null);
            $table->string('class_name')->nullable()->default(null);
            $table->string('school_id')->nullable()->default(null);
            $table->string('school_name')->nullable()->default(null);
            $table->string('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('data_child');
    }
}
