<?php

class EqCapsaicinIo extends Eloquent {

	protected $table = 'eq_capsaicin_io';

	protected $guarded = array();

	public static $rules = array();

	/**
	 * io 레이블의 값이 1이면 보유량 추가, 0이면 불용처분이다.
	 */
}
