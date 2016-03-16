<?php
class EventReport extends Eloquent {

  protected $table = 'event_reports';

  protected $appends = array('has_read', 'is_new', 'is_updated');

  public function user() {
		return $this->belongsTo('User', 'creator_id', 'id');
	}

	public function department() {
		return $this->belongsTo('Department', 'dept_id', 'id');
	}

  public function reads() {
		return $this->hasMany('EventReportRead', 'report_id', 'id');
	}

  public function histories() {
		return $this->hasMany('EventReportHistory', 'report_id', 'id');
	}


  // event_reports와 users의 pivot table인 격. 원래는 event_report_user 또는 user_event_report와 같은
  // table명이어야 하지만 event_report_reads이므로 2번째 parameter로 넣어 줌. 또 event_report_id, user_id가 아니고
  // report_id, user_id이므로 마찬가지로 3, 4번째 parameter로 넣어 준다.
  public function readers() {
		return $this->belongsToMany('User', 'event_report_reads', 'report_id', 'user_id')->withTimestamps();
	}

  public function reportType() {
    return $this->belongsTo('EventReportTemplate','report_type','id');
  }
}
