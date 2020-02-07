<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePopaymentHeadersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('popayment_headers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('ph_no',25)->unique();
            $table->string('ph_rh_no',25);
            $table->double('ph_rembal');
            $table->double('ph_amount');
            $table->char('ph_status')->default('OP');
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
        Schema::dropIfExists('popayment_headers');
    }
}
