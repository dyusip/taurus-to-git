<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateCostSrpNameReqDetails extends Migration
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
            $table->renameColumn('rqd_cost', 'rqd_prod_cost');
            $table->renameColumn('rqd_srp', 'rqd_prod_srp');
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
            $table->renameColumn('rqd_prod_cost', 'rqd_cost');
            $table->renameColumn('rqd_prod_srp', 'rqd_srp');
        });
    }
}
