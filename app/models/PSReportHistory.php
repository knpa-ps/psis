<?php

class PSReportHistory extends Eloquent {
	protected $table = 'ps_reports_history';
	protected $guarded = array();

	public static $rules = array();

	public function user()
	{
		return $this->belongsTo('User', 'creator_id', 'id');
	}

	public function report()
	{
		return $this->belongsTo('PSReport', 'report_id', 'id');
	}
}
