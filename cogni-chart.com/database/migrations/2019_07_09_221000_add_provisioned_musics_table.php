<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddProvisionedMusicsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('provisioned_musics', function (Blueprint $table) {
            $table->string('itunes_base_url')->default('')->after('music_title');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('provisioned_musics', function (Blueprint $table) {
            $table->dropColumn('itunes_base_url');
        });
    }
}
