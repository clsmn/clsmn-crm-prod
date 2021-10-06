<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDataUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('data_user', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('email');
            $table->string('country_code', 10);
            $table->string('phone', 20);
            $table->integer('messenger');
            $table->integer('messenger_id');
            $table->integer('learning');
            $table->integer('learning_id');
            $table->integer('community');
            $table->integer('community_id');
            $table->integer('login_id');
            $table->string('status');
            $table->string('lat_long');
            $table->string('locality');
            $table->string('city');
            $table->string('state');
            $table->string('country');
            $table->string('data_medium');
            $table->enum('email_verified', ['0', '1'])->default('0');
            $table->datetime('last_activity');
            $table->datetime('registered_on');
            $table->enum('moved_to_lead', ['0', '1'])->default('0');
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
        Schema::dropIfExists('data_user');
    }
}
