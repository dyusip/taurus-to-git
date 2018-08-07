<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateInventories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('inventories', function (Blueprint $table) {
            //
            $table->dropColumn('branch_code');
            $table->dropColumn('price');
            $table->dropColumn('quantity');
            $table->dropColumn('cost');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('inventories', function (Blueprint $table) {
            //
            $table->string('branch_code',25)->unique();
            $table->double('price');
            $table->double('cost');
            $table->integer('quantity')->default('0');
        });
    }
}
