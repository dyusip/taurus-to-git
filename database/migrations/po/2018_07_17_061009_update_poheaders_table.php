<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdatePoheadersTable extends Migration
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
            $table->string('sup_add','255')->nullable()->change();
            $table->string('sup_contact','50')->nullable()->change();
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
            $table->string('sup_add','255');
            $table->string('sup_contact','50');
        });
    }
}
