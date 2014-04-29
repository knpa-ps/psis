<?php

class BgtMealCost extends \Eloquent {
	protected $table = 'bgt_meal_costs';
	protected $fillable = [];

	public function department() {
		return $this->belongsTo('Department', 'dept_id' , 'id');
	}

	public function creator() {
		return $this->belongsTo('User', 'creator_id', 'id');
	}

	public function situation() {
		return $this->belongsTo('Code', 'sit_code', 'code')->where('category_code', '=', 'B002');
	}

	public function useType() {
		return $this->belongsTo('Code', 'use_code', 'code')->where('category_code', '=', 'B001');
	}
}