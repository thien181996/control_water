<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('serial', 191);
            $table->integer('distance_max');
            $table->integer('distance_min');
            $table->integer('distance');
            $table->integer('water_status')->default(0);
            $table->integer('pump_status')->default(0);
            $table->integer('auto_status')->default(0);
            $table->integer('tank_status')->default(0);
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
        Schema::dropIfExists('items');
    }
}
