<?php

class EqCapsaicinFirstday extends Eloquent {

	/**
	 * 연도별, 지방청별 캡사이신 희석액 최초보유량이 저장되는 테이블.
	 * 월별 보유량 조회 테이블은 이 값을 기준으로 사용량을 빼서 계산된다.
	 */

	protected $table = 'eq_capsaicin_firstday';

	protected $guarded = array();

	public static $rules = array();

}
