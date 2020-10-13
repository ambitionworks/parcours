<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToActivitySegmentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('activity_segment', function (Blueprint $table) {
            $table->float('avg_speed')->nullable();
            $table->float('avg_power')->nullable();
            $table->float('avg_hr')->nullable();
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
            $table->dropColumn(['avg_speed', 'avg_power', 'avg_hr']);
        });
    }
}
