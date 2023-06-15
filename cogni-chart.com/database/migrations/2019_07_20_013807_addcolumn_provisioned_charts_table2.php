<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddcolumnProvisionedChartsTable2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('provisioned_charts', function (Blueprint $table) {
            $table->string('page_title', 1000)->default('')->after('original_chart_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('provisioned_charts', function (Blueprint $table) {
            $table->dropColumn('page_title');
        });
    }
}
