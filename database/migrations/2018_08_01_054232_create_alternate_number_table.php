<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAlternateNumberTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('alternate_number', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('lead_id');
            $table->string('phone', 20);
            $table->string('name', 100);
            $table->string('relation', 100);
            $table->enum('preferred', ['0', '1'])->default('0');
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
        Schema::dropIfExists('alternate_number');        
    }
}
