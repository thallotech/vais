<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CrowdsourcedDataPrices extends Model 
{
	
	protected $table = 'csdPrices';
	
	protected $fillable = ['email', 'zipCode', 'stateAbrv', 'county', 'price', 'priceUnit'];
    
	public function crowdsourcedDataProduceTypes() {
		return $this->belongsToMany('App\CrowdsourcedDataProduceTypes', 'csdPrice_csdProduceType', 'price_id', 'produceType_id')->withTimestamps();
	}
	
	public function crowdsourcedDataPriceSources() {
		return $this->belongsToMany('App\CrowdsourcedDataPriceSources', 'csdPrice_csdPriceSource', 'price_id', 'priceSource_id')->withTimestamps();
	}
}
