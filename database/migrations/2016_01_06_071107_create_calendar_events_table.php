<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCalendarEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('calendar_events', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title', 255);
            $table->text('description', 1000);
            $table->dateTime('start');
            $table->dateTime('end')->nullable();
            $table->boolean('all_day')->nullable();
            $table->boolean('repeat_week')->nullable()->default(0);
            $table->string('class', 50)->nullable();
            $table->dateTime('repeat_event_end')->nullable();
            $table->integer('model_id')->unsigned();
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
        Schema::drop('calendar_events');
    }
}
