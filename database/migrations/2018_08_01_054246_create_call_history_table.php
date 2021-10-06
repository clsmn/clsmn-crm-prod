<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCallHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('call_history', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('lead_id');
            $table->integer('called_by');
            $table->integer('duration');
            $table->enum('call_type', ['training', 'sale'])->nullable()->default(NULL);
            $table->string('call_record_file');
            $table->text('note');
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
        Schema::dropIfExists('call_history');
    }
}
