<?php

class EqItemSurvey extends Eloquent {

	protected $table = 'eq_item_surveys';

	protected $guarded = array();

	public static $rules = array();

	public function item(){
		return $this->belongsTo('EqItem', 'item_id','id');
	}
	public function datas(){
		return $this->hasMany('EqItemSurveyData','survey_id','id');
	}

	public function responses() {
		return $this->hasMany('EqItemSurveyResponse','survey_id','id');
	}
}
