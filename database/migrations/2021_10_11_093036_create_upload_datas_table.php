<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUploadDatasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::create('upload_datas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('date');
            $table->string('data_medium');
            $table->integer('repeatlLeads')->default(0);
            $table->integer('newLeads')->default(0);
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
        Schema::dropIfExists('upload_datas');
    }
}
