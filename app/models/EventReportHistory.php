<?php

class EventReportHistory extends Eloquent {
    protected $softDelete = true;
	protected $table = 'event_reports_history';

	public function user() {
		return $this->belongsTo('User', 'creator_id', 'id');
	}

	public function report() {
		return $this->belongsTo('EventReport', 'report_id', 'id');
	}

	public function scopeLastest($query) {
		return $query->orderBy('created_at', 'desc')->take(1);
	}
}
