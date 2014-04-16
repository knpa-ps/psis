<?php

class PSReport extends Eloquent {

	protected $table = 'ps_reports';
	protected $guarded = array();

	public static $rules = array();

	public function user()
	{
		return $this->belongsTo('User', 'creator_id', 'id');
	}

	public function histories()
	{
		return $this->hasMany('PSReportHistory', 'report_id', 'id');
	}
}
