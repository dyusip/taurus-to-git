<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateTfAppbyToDefaultTransferHeadersTable extends Migration
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
            $table->string('tf_appby','25')->nullable()->change();
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
            $table->string('tf_appby','25');
        });
    }
}
