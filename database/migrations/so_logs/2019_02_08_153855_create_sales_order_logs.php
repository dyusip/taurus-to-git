<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSalesOrderLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('sol_code','25');
            $table->string('sol_prod_name','255');
            $table->string('sol_remarks','255');
            $table->char('sol_status','2')->default('AC');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sales_logs');
    }
}
