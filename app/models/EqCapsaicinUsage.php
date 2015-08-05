<?php

class EqCapsaicinUsage extends Eloquent {

	protected $table = 'eq_capsaicin_usage';

	protected $guarded = array();

	public static $rules = array();


	public function event() {
		return $this->belongsTo('EqCapsaicinEvent','event_id','id');
	}

	public function node() {
		return $this->belongsTo('EqSupplyManagerNode','user_node_id','id');
	}

	public function cross() {
		return $this->hasOne('EqCapsaicinCrossRegion', 'usage_id', 'id');
	}
}
