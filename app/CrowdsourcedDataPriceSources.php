<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\SluggableInterface;
use Cviebrock\EloquentSluggable\SluggableTrait;

class CrowdsourcedDataPriceSources extends Model implements SluggableInterface
{
	use SluggableTrait;

	protected $sluggable = [
		'build_from' => 'source',
		'save_to'    => 'slug',
	];
	
	protected $table = 'csdPriceSources';
	
	protected $fillable = ['source', 'slug'];
	
	public function crowdsourcedDataPrices() {
		return $this->belongsToMany('App\CrowdsourcedDataPrices', 'csdPrice_csdPriceSource', 'priceSource_id', 'price_id')->withTimestamps();
	}
}
