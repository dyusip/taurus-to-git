<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSrHeadersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sr_headers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('sr_code','25')->primary_key();
            $table->string('so_code','25');
            $table->date('sr_date');
            $table->string('sr_prepby','25');
            $table->double('sr_total');
            $table->char('sr_status','2')->default('OP');
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
        Schema::dropIfExists('sr_headers');
    }
}
