<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropandaddcolumnProvisionedChartsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('provisioned_charts', function (Blueprint $table) {
            $table->dropColumn('page_title');
            $table->string('original_chart_name')->default('')->after('uri');
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
            $table->dropColumn('original_chart_name');
            $table->string('page_title', 1000)->default('')->after('uri');
        });
    }
}
