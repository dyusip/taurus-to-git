<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class StatusMakeDefaultInTransferHeadersTable extends Migration
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
            $table->string('tf_status','2')->default('NA')->change();
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
            $table->string('tf_status','2');
        });
    }
}
