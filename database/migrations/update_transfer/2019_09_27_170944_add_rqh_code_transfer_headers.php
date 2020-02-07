<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRqhCodeTransferHeaders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transfer_headers', function (Blueprint $table) {
            //
            $table->string('rqh_code','25')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transfer_headers', function (Blueprint $table) {
            //
            $table->dropColumn('rqh_code');
        });
    }
}
