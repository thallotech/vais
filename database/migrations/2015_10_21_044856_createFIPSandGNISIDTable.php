<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFIPSandGNISIDTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fipsAndGNISID', function (Blueprint $table) {
            $table->increments('id');
            $table->string('fipsStateCode');
			$table->string('uspsCode');
			$table->string('name');
			$table->string('gnisidCode');
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
        Schema::drop('fipsAndGNISID');
    }
}
