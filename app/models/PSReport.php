<?php
use Carbon\Carbon;

class PSReport extends Eloquent {
    protected $softDelete = true;

    protected $appends = array('has_read', 'is_new', 'is_updated');

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

	public function readers() {
		return $this->belongsToMany('User', 'ps_report_reads', 'report_id', 'user_id')->withTimestamps();
	}

	public function getHasReadAttribute() {
		$user = Sentry::getUser();
		
		if ($user === null) {
			return false;
		}

		return $this->readers()->get()->contains($user->id);
	}

	public function getIsNewAttribute() {
		$now = Carbon::now();
		return $now->diffInDays($this->created_at) <= 1;
	}

	public function getIsUpdatedAttribute() {
		$now = Carbon::now();
		return $now->diffInDays($this->updated_at) <= 1;
	}
}
