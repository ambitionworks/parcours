<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMetricsFieldsToActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('activities', function (Blueprint $table) {
            if (!Schema::hasColumn('ftp', 'hr_resting', 'hr_max', 'hr_lt')) {
                $table->integer('ftp')->nullable();
                $table->integer('hr_resting')->nullable();
                $table->integer('hr_max')->nullable();
                $table->integer('hr_lt')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->dropColumn(['ftp', 'hr_resting', 'hr_max', 'hr_lt']);
        });
    }
}
