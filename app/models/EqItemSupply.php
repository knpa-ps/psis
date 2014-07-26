<?php

class EqItemSupply extends Eloquent {
	
	protected $table = 'eq_item_supplies';
	
	protected $guarded = array();

	public static $rules = array();

	public function item(){
		return $this->belongsTo('User','creator_id','id');
	}
}
