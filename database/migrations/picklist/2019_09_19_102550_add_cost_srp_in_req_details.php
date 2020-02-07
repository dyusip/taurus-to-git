<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCostSrpInReqDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('req_details', function (Blueprint $table) {
            //
            $table->double('rqd_cost');
            $table->double('rqd_srp');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('req_details', function (Blueprint $table) {
            //
            $table->dropColumn('rqd_cost');
            $table->dropColumn('rqd_srp');
        });
    }
}
