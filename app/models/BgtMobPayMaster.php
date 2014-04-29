<?php

class BgtMobPayMaster extends \Eloquent {
	protected $table = 'bgt_mobpay_masters';
	protected $fillable = [];

	public function details() {
		return $this->hasMany('BgtMobPayDetail', 'master_id', 'id');
	}

	public function department() {
		return $this->belongsTo('Department', 'dept_id' , 'id');
	}

	public function creator() {
		return $this->belongsTo('User', 'creator_id', 'id');
	}

	public function situation() {
		return $this->belongsTo('Code', 'sit_code', 'code')->where('category_code', '=', 'B002');
	}
}