<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBiReplicatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bi_replicates', function (Blueprint $table) {
            $table->increments('id');
            $table->string('bir_branch_code',25);
            $table->string('bir_prod_code',25);
            $table->date('bir_date');
            $table->double('bir_price');
            $table->double('bir_cost');
            $table->integer('bir_quantity')->default('0');
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
        Schema::dropIfExists('bi_replicates');
    }
}
