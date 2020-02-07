<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePriceChangesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('price_changes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('pc_branch_code',25);
            $table->date('pc_date');
            $table->double('pc_cost');
            $table->double('pc_srp');
            $table->char('pc_status',2)->default('AC');
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
        Schema::dropIfExists('price_changes');
    }
}
