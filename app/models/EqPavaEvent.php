<?php

class EqPavaEvent extends Eloquent {
	protected $table = "eq_pava_event";

	protected $guarded = array();

	public static $rules = array();

	public function children(){
		return $this->hasMany('EqPavaUsage','event_id','id');
	}

	public function regionNode() {
		return $this->belongsTo('EqSupplyManagerNode', 'node_id', 'id');
	}
}
