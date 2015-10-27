<?php

class EqItemSupplySet extends Eloquent {

	protected $table = 'eq_item_supplies_set';

	protected $guarded = array();

	public static $rules = array();

	public function item(){
		return $this->belongsTo('EqItem', 'item_id','id');
	}

	public function node(){
		return $this->belongsTo('EqSupplyManagerNode', 'from_node_id', 'id');
	}

	public function children(){
		return $this->hasMany('EqItemSupply','supply_set_id','id');
	}

	public function managedChildren() {
		return $this->hasMany('EqSupplyManagerNode','parent_manager_node','from_node_id');
	}
}
