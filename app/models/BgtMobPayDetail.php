<?php

class BgtMobPayDetail extends \Eloquent {
	protected $table = 'bgt_mobpay_details';

	public function department() {
		return $this->belongsTo('Department', 'dept_id', 'id');
	}

	public function rank() {
		return $this->belongsTo('Code', 'rank_code', 'code')->where('category_code', '=', 'H001');
	}
}