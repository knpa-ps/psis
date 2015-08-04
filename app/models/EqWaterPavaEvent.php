<?php

class EqWaterPavaEvent extends Eloquent {

	protected $table = "eq_waterpava_event";

	protected $guarded = array();

	public static $rules = array();

	public function node() {
		return $this->belongsTo('EqSupplyManagerNode', 'node_id', 'id');
	}
}
