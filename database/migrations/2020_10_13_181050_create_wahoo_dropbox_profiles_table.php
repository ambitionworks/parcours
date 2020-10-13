<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWahooDropboxProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wahoo_dropbox_profiles', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->foreignId('user_id');
            $table->timestamp('highwater')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('wahoo_dropbox_profiles');
    }
}
