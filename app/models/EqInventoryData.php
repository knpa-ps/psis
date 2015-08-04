<?php

class EqInventoryData extends Eloquent {

	protected $table = 'eq_inventories_data';

	protected $guarded = array();

	public static $rules = array();

	public function parentSet(){
		return $this->belongsTo('EqInventorySet','inventory_set_id','id');
	}

}
