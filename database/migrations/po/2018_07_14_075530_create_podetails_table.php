<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePodetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('po_details', function (Blueprint $table) {
            //$table->increments('id');
            $table->string('pod_code','25');
            $table->string('prod_code','25');
            $table->string('prod_name','255');
            $table->string('prod_uom','25');
            $table->integer('prod_qty');
            $table->double('prod_price');
            $table->double('prod_amount');
            //$table->char('status','2')->default('OP');

            $table->primary(['pod_code','prod_code']);

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
        Schema::dropIfExists('po_details');
    }
}
