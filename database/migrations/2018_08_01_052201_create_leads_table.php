<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLeadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('data_user_id');

            //Data bank fields
            $table->string('name');
            $table->string('email')->nullable();
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
            $table->string('lat_long')->nullable()->default(NULL);
            $table->string('locality')->nullable()->default(NULL);
            $table->string('city')->nullable()->default(NULL);
            $table->string('state')->nullable()->default(NULL);
            $table->string('country')->nullable()->default(NULL);
            $table->string('data_medium');
            $table->enum('email_verified', ['0', '1'])->default('0');
            $table->datetime('last_activity')->nullable()->default(NULL);
            $table->datetime('registered_on')->nullable()->default(NULL);

            $table->integer('assigned_by')->default('0');
            $table->integer('assigned_to')->default('0');
            $table->enum('lead_status', ['open', 'hot', 'mild', 'cold', 'dead', 'sale'])->default('open');
            $table->integer('reason_id')->default('0');
            $table->integer('lead_stage')->nullable()->default(NULL);
            $table->timestamp('next_follow_up')->default(NULL);
            $table->timestamp('last_call')->default(NULL);
            $table->date('call_date');
            $table->enum('done', ['0', '1'])->default('0');
            $table->enum('preferred', ['0', '1'])->default('1');
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
        Schema::dropIfExists('leads');
    }
}
