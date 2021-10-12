<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLeadPerformancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lead_performances', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('source');
            $table->integer('totalLeads')->default(0);
            $table->integer('open')->default(0);
            $table->integer('hot')->default(0);
            $table->integer('mild')->default(0);
            $table->integer('cold')->default(0);
            $table->integer('dead')->default(0);
            $table->integer('sale')->default(0);
            $table->integer('no_answer')->default(0);
            $table->integer('busy')->default(0);
            $table->integer('not_interested')->default(0);
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
        Schema::dropIfExists('lead_performances');
    }
}
