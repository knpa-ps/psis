<?php

class EqItemCode extends Eloquent {

	protected $table = 'eq_item_code';

	
	public function category() {
		return $this->belongsTo('EqCategory', 'category_id', 'id');
	}

	public function items() {
		return $this->hasMany('EqItem','item_code','code')->where('is_active','=',1);
	}

}
