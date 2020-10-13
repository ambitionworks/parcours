<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldToActivitySegmentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('activity_segment', function (Blueprint $table) {
            $table->float('avg_cadence')->nullable();
            $table->float('normalized_power')->nullable();
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
            $table->dropColumn(['avg_cadence', 'normalized_power']);
        });
    }
}
