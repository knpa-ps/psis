<?php

class EqItemType extends Eloquent {

	protected $table = "eq_item_types";

	protected $guarded = array();

	public static $rules = array();

	public function acquires(){
		return $this->hasOne('EqItmeAcquire','type_id','id');
	}
}
