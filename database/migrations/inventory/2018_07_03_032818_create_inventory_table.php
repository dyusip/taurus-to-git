<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInventoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('branch_code',25)->unique();
            $table->string('code',25)->unique();
            $table->string('name',255);
            $table->string('desc',150);
            $table->double('price');
            $table->double('cost');
            $table->integer('quantity')->default('0');
            $table->string('uom',50);
            $table->integer('pqty');
            $table->char('status',2)->default('AC');
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
        Schema::dropIfExists('inventories');
    }
}