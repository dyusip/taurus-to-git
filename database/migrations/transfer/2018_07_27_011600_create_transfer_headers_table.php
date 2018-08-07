<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransferHeadersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transfer_headers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('tf_code','25');
            $table->string('from_branch','25');
            $table->string('to_branch','25');
            $table->date('tf_date');
            $table->string('tf_prepby','25');
            $table->string('tf_appby','25');
            $table->double('tf_amount');
            $table->char('tf_status','2');
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
        Schema::dropIfExists('transfer_headers');
    }
}
