<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddProdCodeInPriceChanges extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('price_changes', function (Blueprint $table) {
            //
            $table->string('pc_prod_code',25);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('price_changes', function (Blueprint $table) {
            //
            $table->removeColumn('pc_prod_code');
        });
    }
}
