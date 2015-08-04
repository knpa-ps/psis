<?php

class EqItemSurvey extends Eloquent {

	protected $table = 'eq_item_surveys';

	protected $guarded = array();

	public static $rules = array();

	public function node(){
		return $this->belongsTo('EqSupplyManagerNode','node_id','id');
	}

	public function item(){
		return $this->belongsTo('EqItem', 'item_id','id');
	}
	public function datas(){
		return $this->hasMany('EqItemSurveyData','survey_id','id');
	}

	public function responses() {
		return $this->hasMany('EqItemSurveyResponse','survey_id','id');
	}

	public function isResponsed($nodeId){
		$responses = EqItemSurveyResponse::where('node_id','=',$nodeId)->where('survey_id','=',$this->id)->get();

		if (sizeof($responses) !== 0) {
			return 1;
		} else {
			return 0;
		}
	}
}
