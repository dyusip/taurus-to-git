<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateSodProdLessToDoubleInSoDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('so_details', function (Blueprint $table) {
            //
            $table->float('sod_less')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('so_details', function (Blueprint $table) {
            //
            $table->integer('sod_less')->change();
        });
    }
}
