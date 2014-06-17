<?php

class EqDomain extends Eloquent {
	protected $table = 'eq_domains';

	public function categories() {
		return $this->hasMany('EqCategory', 'domain_id', 'id');
	}

}
