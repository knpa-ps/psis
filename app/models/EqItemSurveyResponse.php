<?php

class EqItemSurveyResponse extends Eloquent {

	protected $table = 'eq_item_survey_responses';

	protected $guarded = array();

	public static $rules = array();

	public function item(){
		return $this->belongsTo('EqItem', 'item_id','id');
	}
}
