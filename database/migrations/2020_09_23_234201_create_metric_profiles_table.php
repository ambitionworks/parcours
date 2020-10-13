<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMetricProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('metric_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->string('gender')->default('male');
            $table->integer('ftp')->nullable();
            $table->integer('hr_resting')->nullable();
            $table->integer('hr_max')->nullable();
            $table->integer('hr_lt')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('metric_profiles');
    }
}
