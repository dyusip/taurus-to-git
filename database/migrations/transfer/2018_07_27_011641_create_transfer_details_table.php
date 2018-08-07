<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransferDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transfer_details', function (Blueprint $table) {
            //$table->increments('id');
            $table->string('tf_code','25');
            $table->string('tf_prod_code','25');
            $table->string('tf_prod_name','255');
            $table->string('tf_prod_uom','25');
            $table->integer('tf_prod_qty');
            $table->double('tf_prod_price');
            $table->double('tf_prod_amount');
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
        Schema::dropIfExists('transfer_details');
    }
}
