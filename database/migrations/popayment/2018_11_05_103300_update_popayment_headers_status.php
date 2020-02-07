<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdatePopaymentHeadersStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('popayment_headers', function (Blueprint $table) {
            //
            $table->string('ph_status',2)->default('OP')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('popayment_headers', function (Blueprint $table) {
            //
            $table->string('ph_status')->default('OP')->change();
        });
    }
}
