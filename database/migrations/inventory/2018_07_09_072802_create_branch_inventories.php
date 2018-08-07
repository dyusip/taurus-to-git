<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBranchInventories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('branch_inventories', function (Blueprint $table) {
            //$table->increments('id');
            $table->string('branch_code',25);
            $table->string('prod_code',25);
            $table->double('price');
            $table->double('cost');
            $table->integer('quantity')->default('0');
            $table->timestamps();

            $table->primary(['branch_code','prod_code']);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('branch_inventories');
    }
}
