<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStartEndTimesToActivitySegmentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('activity_segment', function (Blueprint $table) {
            $table->bigInteger('start_time');
            $table->bigInteger('end_time');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('activity_segment', function (Blueprint $table) {
            $table->dropColumn(['start_time', 'end_time']);
        });
    }
}
