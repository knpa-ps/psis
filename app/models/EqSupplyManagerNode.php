<?php

class EqSupplyManagerNode extends Eloquent {

	protected $table = 'eq_supply_manager_nodes';

	protected $guarded = array();

	public static $rules = array();

	public function supplies(){
		return $this->hasMany('EqItemSupplySet', 'from_node_id', 'id');
	}

	public function manager() {
		return $this->hasMany('User', 'manager_id', 'id');
	}

	public function parent() {
		return $this->belongsTo('EqSupplyManagerNode', 'parent_id', 'id');
	}

	public function children() {
		return $this->hasMany('EqSupplyManagerNode', 'parent_id', 'id');
	}
}
