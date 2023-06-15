<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChartRankingItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chart_ranking_items', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('chart_artist');
            $table->string('chart_music');
            $table->string('artist_id')->default('');
            $table->string('music_id')->default('');
            $table->timestamps();
            $table->index(['chart_artist', 'chart_music']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('chart_ranking_items');
    }
}
