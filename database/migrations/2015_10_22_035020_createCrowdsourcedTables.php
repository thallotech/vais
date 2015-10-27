<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCrowdsourcedTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('csdPrices', function (Blueprint $table) {
            $table->increments('id');
			$table->text('email');
			$table->string('zipCode');
			$table->string('price');
			$table->string('priceUnit');
            $table->timestamps();
		});
		Schema::create('csdProduceTypes', function (Blueprint $table) {
            $table->increments('id');
			$table->string('produceName');
            $table->timestamps();
		});
		Schema::create('csdPriceSources', function (Blueprint $table) {
			$table->increments('id');
			$table->string('source');
			$table->timestamps();
		});
		Schema::create('csdPrice_csdProduceType', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('price_id')->unsigned()->index();
			$table->foreign('price_id')->references('id')->on('csdPrices')->onDelete('cascade');
			$table->integer('produceType_id')->unsigned()->index();
			$table->foreign('produceType_id')->references('id')->on('csdProduceTypes')->onDelete('cascade');
			$table->timestamps();
		});
		Schema::create('csdPrice_csdPriceSource', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('price_id')->unsigned()->index();
			$table->foreign('price_id')->references('id')->on('csdPrices')->onDelete('cascade');
			$table->integer('priceSource_id')->unsigned()->index();
			$table->foreign('priceSource_id')->references('id')->on('csdPriceSources')->onDelete('cascade');
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
        Schema::drop([ 'csdPrices', 'csdProduceTypes', 'csdPriceSources', 'csdPrice_csdProduceType', 'csdPrice_csdPriceSource' ]);
    }
}
