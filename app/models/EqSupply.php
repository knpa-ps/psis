<?php

class EqSupply extends Eloquent {

	protected $table = 'eq_supplies';

	protected $guarded = array();

	public static $rules = array();

	public function details() {
		return $this->hasMany('EqSupplyDetail', 'supply_id', 'id');
	}

	public function department() {
		return $this->belongsTo('Department', 'supply_dept_id', 'id');
	}

	public function creator() {
		return $this->belongsTo('User', 'creator_id', 'id');
	}

	public function item() {
		return $this->belongsTo('EqItem', 'item_id', 'id');
	}
}
