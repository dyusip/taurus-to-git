<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePopaymentDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('popayment_details', function (Blueprint $table) {
            //$table->increments('id');
            $table->string('pd_no',25);
            $table->string('pd_paymentno',25);
            $table->date('pd_date');
            $table->string('pd_type',5);
            $table->double('pd_amount');
            $table->string('pd_checkno','50')->nullable();
            $table->string('pd_bank','100')->nullable();
            $table->date('pd_checkdate');

            $table->primary(['pd_no','pd_paymentno']);
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
        Schema::dropIfExists('popayment_details');
    }
}
