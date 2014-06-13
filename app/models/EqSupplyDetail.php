<?php

class EqSupplyDetail extends Eloquent {

	protected $table = 'eq_supply_details';

	protected $guarded = array();

	public static $rules = array();

	public function master() {
		return $this->belongsTo('EqSupply', 'supply_id', 'id');
	}

	public function department() {
		return $this->belongsTo('Department', 'target_dept_id', 'id');
	}
}
