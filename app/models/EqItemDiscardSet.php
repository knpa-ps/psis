<?php

class EqItemDiscardSet extends Eloquent {

	protected $table = 'eq_item_discard_set';

	protected $guarded = array();

	public static $rules = array();

	public function children() {
		return $this->hasMany('EqItemDiscardData','discard_set_id','id');
	}

	public function node() {
		return $this->belongsTo('EqSupplyManagerNode', 'node_id', 'id');
	}
}
