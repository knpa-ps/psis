<?php

class PSReport extends Eloquent {
    protected $softDelete = true;

	protected $table = 'ps_reports';

	public function user() {
		return $this->belongsTo('User', 'creator_id', 'id');
	}

	public function department() {
		return $this->belongsTo('Department', 'dept_id', 'id');
	}

	public function reads() {
		return $this->hasMany('PSReportRead', 'report_id', 'id');
	}

	public function histories() {
		return $this->hasMany('PSReportHistory', 'report_id', 'id');
	}
}
