<?php

class EqInventorySet extends Eloquent {

	protected $table = 'eq_inventories_set';

	protected $guarded = array();

	public static $rules = array();

	public function children(){
		return $this->hasMany('EqInventoryData','inventory_set_id','id');
	}

	public function ownerNode() {
		return $this->belongsTo('EqSupplyManagerNode','node_id','id');
	}

	public function item() {
		return $this->belongsTo('EqItem', 'item_id','id');
	}
}
