<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPoAppbyToPoheadersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('po_headers', function (Blueprint $table) {
            //
            $table->string('po_appby','25')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('po_headers', function (Blueprint $table) {
            //
            $table->dropColumn('po_appby','25');
        });
    }
}
