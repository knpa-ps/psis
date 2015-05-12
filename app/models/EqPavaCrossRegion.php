<?php

class EqPavaCrossRegion extends Eloquent {
	protected $table = 'eq_pava_cross_region';

	protected $guarded = array();

	public static $rules = array();

	// 타 지방청에 동원된 경우의 사용량이 저장된다.
	public function io(){
		return $this->belongsTo('EqPavaIo','io_id','id');
	}
}
