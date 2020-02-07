<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateSalesLogsAddFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sales_logs', function (Blueprint $table) {
            //
            $table->string('sol_prod_code','25');
            $table->integer('sol_prod_qty');
            $table->double('sol_prod_price');
            $table->double('sol_prod_amount');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sales_logs', function (Blueprint $table) {
            //
            $table->dropColumn('sol_prod_code');
            $table->dropColumn('sol_prod_qty');
            $table->dropColumn('sol_prod_price');
            $table->dropColumn('sol_prod_amount');
        });
    }
}
