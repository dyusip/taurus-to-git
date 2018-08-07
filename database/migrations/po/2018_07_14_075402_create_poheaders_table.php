<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePoheadersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('po_headers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('po_code','25')->unique();
            $table->string('po_prepby','25');
            $table->date('req_date');
            $table->string('term','25');
            $table->date('po_date');
            $table->string('sup_name','100');
            $table->string('sup_add','255');
            $table->string('sup_contact','50');
            $table->double('total');
            $table->char('status','2')->default('PD');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('po_headers');
    }
}
