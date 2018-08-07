<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSoHeadersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('so_headers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('so_code','25')->primary_key();
            $table->string('branch_code','25');
            $table->string('jo_code','25');
            $table->string('so_prepby','25');
            $table->string('so_salesman','25');
            $table->string('so_mechanic','25')->nullable();
            $table->string('cust_name','25');
            $table->string('cust_add','25')->nullable();
            $table->string('cust_contact','25')->nullable();
            $table->date('so_date');
            $table->double('serv_charge')->default(0);
            $table->double('amount_rec')->default(0);
            $table->double('so_amount');
            $table->char('so_status','2')->default('PD');

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
        Schema::dropIfExists('so_headers');
    }
}
