<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateTransferDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transfer_details', function (Blueprint $table) {
            //
            $table->renameColumn('tf_code', 'td_code');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transfer_details', function (Blueprint $table) {
            //
            $table->renameColumn('td_code', 'tf_code');
        });
    }
}
