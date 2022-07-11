<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDelightSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('delight_sales', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('learning_user_id')->nullable();
            $table->string('product_name')->nullable();
            $table->string('client_name')->nullable();
            $table->string('country_code')->nullable();
            $table->string('phone')->nullable();
            $table->integer('followup_1')->nullable();
            $table->integer('followup_2')->nullable();
            $table->longText('comment');
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
        Schema::dropIfExists('delight_sales');
    }
}
