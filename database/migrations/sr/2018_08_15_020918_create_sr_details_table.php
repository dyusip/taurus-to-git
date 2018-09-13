<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSrDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sr_details', function (Blueprint $table) {
            //$table->increments('id');
            $table->string('srd_code','25');
            $table->string('srd_prod_code','25');
            $table->string('srd_prod_name','255');
            $table->string('srd_prod_uom','25');
            $table->integer('srd_prod_qty');
            $table->double('srd_prod_price');
            $table->integer('srd_less');
            $table->double('sod_prod_amount');
            $table->char('status','2');
            $table->timestamps();
            $table->primary(['srd_code','srd_prod_code']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sr_details');
    }
}
