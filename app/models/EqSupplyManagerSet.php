<?php

class EqSupplyManagerSet extends Eloquent {

	protected $table = 'eq_supply_manager_set';

	protected $guarded = array();

	public static $rules = array();

	public function node(){
		return $this->belongsTo('EqSupplyManagerNode', 'node_id', 'id');
	}

	public function manager(){
		return $this->belongsTo('User', 'manager_id', 'id');
	}
}
