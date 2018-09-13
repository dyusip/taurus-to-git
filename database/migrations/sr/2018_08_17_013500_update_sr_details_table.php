<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateSrDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sr_details', function (Blueprint $table) {
            //
            $table->renameColumn('sod_prod_amount', 'srd_prod_amount');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sr_details', function (Blueprint $table) {
            //
            $table->renameColumn('srd_prod_amount', 'sod_prod_amount');
        });
    }
}
