<?php

/**
* EqCategory
*/
class EqCategory extends Eloquent {
	protected $table = 'eq_categories';

	public function domain() {
		return $this->belongsTo('EqDomain', 'domain_id', 'id');
	}

	public function items() {
		return $this->hasMany('EqItem', 'category_id', 'id');
	}
}
