<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReceivingDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('receiving_detail', function (Blueprint $table) {
            //$table->increments('id');
            $table->string('rd_code','25');
            $table->string('rd_prod_code','25');
            $table->string('rd_prod_name','255');
            $table->string('rd_prod_uom','25');
            $table->integer('rd_prod_qty');
            $table->double('rd_prod_price');
            $table->double('rd_prod_amount');
            $table->char('rd_status','2');

            $table->primary(['rd_code','rd_prod_code']);
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
        Schema::dropIfExists('receiving_detail');
    }
}
