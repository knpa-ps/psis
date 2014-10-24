<?php

class EqItemSupply extends Eloquent {
	
	protected $table = 'eq_item_supplies';
	
	protected $guarded = array();

	public static $rules = array();

	public function supplySet(){
		return $this->hasOne('EqItemSupplySet', 'id', 'supply_set_id');
	}
	
}
