<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCountyList extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('countyList', function (Blueprint $table) {
            $table->increments('id');
            $table->string('stateCode');
			$table->string('districtCode');
			$table->string('countyCode');
			$table->string('name');
			$table->string('historyFlag');
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
        Schema::drop('countyList');
    }
}
