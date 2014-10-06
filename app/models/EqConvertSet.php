<?php

class EqConvertSet extends Eloquent {

	protected $table = 'eq_convert_set';

	protected $guarded = array();

	public static $rules = array();

	public function item() {
		return $this->belongsTo('EqItem','item_id','id');
	}

	public function fromNode() {
		return $this->belongsTo('EqSupplyManagerNode','from_node_id','id');
	}

	public function targetNode() {
		return $this->belongsTo('EqSupplyManagerNode','target_node_id','id');
	}

	public function children() {
		return $this->hasMany('EqConvertData','convert_set_id','id');
	}
}
