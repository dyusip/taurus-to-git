<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdatePoHeadersSupplier extends Migration
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
            $table->dropColumn('sup_name','sup_add','sup_contact');
            $table->string('sup_code','25');
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
            $table->string('sup_name','100');
            $table->string('sup_add','255');
            $table->string('sup_contact','50');
            $table->dropColumn('sup_code');
        });
    }
}
