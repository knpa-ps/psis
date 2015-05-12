<?php

class EqPavaUsage extends Eloquent {
	
	protected $table = 'eq_pava_usage';

	protected $guarded = array();

	public static $rules = array();

	public function event() {
		return $this->belongsTo('EqPavaEvent','event_id','id');
	}

	public function node() {
		return $this->belongsTo('EqSupplyManagerNode','user_node_id','id');
	}

	public function cross() {
		return $this->hasOne('EqPavaCrossRegion', 'usage_id', 'id');
	}
}
