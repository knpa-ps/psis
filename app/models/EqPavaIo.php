<?php

class EqPavaIO extends Eloquent {

	protected $table = 'eq_pava_io';

	protected $guarded = array();

	public static $rules = array();

	public function node() {
		return $this->belongsTo('EqSupplyManagerNode', 'node_id', 'id');
	}

	public function type() {

		switch ($this->sort) {
			case 'training':
				return "훈련";
				break;
			case 'lost':
				return "소실";
				break;
			default:
				return "미지정";
				break;
		}
	}
}
