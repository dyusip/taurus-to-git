<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInvPositionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inv_positions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('ip_branch_code','25');
            $table->double('ip_cost');
            $table->double('ip_srp');
            $table->date('ip_date');
            $table->char('ip_status','2')->default('OP');
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
        Schema::dropIfExists('inv_positions');
    }
}
