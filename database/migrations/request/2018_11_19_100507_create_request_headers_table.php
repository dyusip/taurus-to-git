<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRequestHeadersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('request_headers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('rqh_no',25)->unique();
            $table->date('rqh_date');
            $table->string('rqh_branch',25);
            $table->double('rqh_total');
            $table->char('status',2);
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
        Schema::dropIfExists('request_headers');
    }
}
