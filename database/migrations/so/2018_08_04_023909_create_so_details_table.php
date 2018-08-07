<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSoDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('so_details', function (Blueprint $table) {
            //$table->increments('id');
            $table->string('sod_code','25');
            $table->string('sod_prod_code','25');
            $table->string('sod_prod_name','255');
            $table->string('sod_prod_uom','25');
            $table->integer('sod_prod_qty');
            $table->double('sod_prod_price');
            $table->string('sod_less','3')->default('0%');
            $table->double('sod_prod_amount');
            $table->timestamps();
            $table->primary(['sod_code','sod_prod_code']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('so_details');
    }
}
