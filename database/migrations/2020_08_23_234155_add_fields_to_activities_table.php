<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->timestamp('processed_at')->nullable()->after('updated_at');
            $table->timestamp('performed_at')->nullable()->after('processed_at');
            $table->integer('tz_offset')->default(0);
            $table->text('name')->nullable();
            $table->text('description')->nullable();
            $table->float('duration')->nullable();
            $table->float('active_duration')->nullable();
            $table->boolean('stationary')->default(false);
            $table->boolean('has_laps')->default(false);
            $table->float('distance')->nullable();
            $table->float('ascent')->nullable();
            $table->float('descent')->nullable();
            $table->float('normalized_power')->nullable();
            $table->float('avg_power')->nullable();
            $table->float('avg_hr')->nullable();
            $table->float('avg_speed')->nullable();
            $table->float('avg_cadence')->nullable();
            $table->float('max_power')->nullable();
            $table->float('max_hr')->nullable();
            $table->float('max_speed')->nullable();
            $table->float('max_cadence')->nullable();
            $table->multipoint('route')->nullable();
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
            $table->dropColumn([
                'processed_at',
                'performed_at',
                'tz_offset',
                'name',
                'description',
                'duration',
                'active_duration',
                'distance',
                'ascent',
                'descent',
                'normalized_power',
                'avg_power',
                'avg_hr',
                'avg_speed',
                'max_power',
                'max_hr',
                'max_speed',
                'max_cadence',
                'route',
            ]);
        });
    }
}
