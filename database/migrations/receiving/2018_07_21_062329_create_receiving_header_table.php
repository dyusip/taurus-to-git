<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReceivingHeaderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('receiving_header', function (Blueprint $table) {
            $table->increments('id');
            $table->string('rh_no','25')->unique();
            $table->string('rh_po_no','25');
            $table->string('rh_si_no','25');
            $table->string('rh_branch_code','25');
            $table->date('rh_date');
            $table->double('rh_amount');
            $table->string('rh_prepby','25');
            $table->char('rh_status','2');
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
        Schema::dropIfExists('receiving_header');
    }
}
