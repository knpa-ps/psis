<?php

class PSTemp extends Eloquent {

	protected $table = 'ps_reports_temp';
	protected $guarded = array();

	public static $rules = array();

	public function user()
	{
		return $this->belongsTo('User', 'creator_id', 'id');
	}
}
