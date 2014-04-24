<?php

class PSReportHistory extends Eloquent {
    protected $softDelete = true;
	protected $table = 'ps_reports_history';

	public function user() {
		return $this->belongsTo('User', 'creator_id', 'id');
	}

	public function report() {
		return $this->belongsTo('PSReport', 'report_id', 'id');
	}
}
