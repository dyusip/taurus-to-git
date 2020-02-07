<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRequestDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('request_details', function (Blueprint $table) {
            //$table->increments('id');
            $table->string('rqd_code','25');
            $table->string('rqd_prod_code','25');
            $table->string('rqd_prod_name','255');
            $table->string('rqd_prod_uom','25');
            $table->integer('rqd_prod_qty');
            $table->double('rqd_prod_price');
            $table->double('rqd_prod_amount');
            $table->timestamps();
            $table->primary(['rqd_code','rqd_prod_code']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('request_details');
    }
}
