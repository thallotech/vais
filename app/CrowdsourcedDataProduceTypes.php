<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\SluggableInterface;
use Cviebrock\EloquentSluggable\SluggableTrait;

class CrowdsourcedDataProduceTypes extends Model implements SluggableInterface
{
    //
	use SluggableTrait;

	protected $sluggable = [
		'build_from' => 'produceName',
		'save_to'    => 'slug',
	];
	
	protected $table = 'csdProduceTypes';
	
	protected $fillable = ['produceName', 'slug'];
	
	public function crowdsourcedDataPrices() {
		return $this->belongsToMany('App\CrowdsourcedDataPrices', 'csdPrice_csdProduceType', 'produceType_id', 'price_id')->withTimestamps();
	}
}
