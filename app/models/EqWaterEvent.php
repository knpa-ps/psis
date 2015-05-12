<?php

class EqWaterEvent extends Eloquent {

	protected $table = "eq_water_event";

	protected $guarded = array();

	public static $rules = array();

	public function node() {
		return $this->belongsTo('EqSupplyManagerNode', 'node_id', 'id');
	}
}
