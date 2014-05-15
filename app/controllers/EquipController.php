<?php

class EquipController extends BaseController {
	protected $service;

	public function __construct() {
		$this->service = new EqService;
	}

	public function displayItems() {

		return View::make('equip.items');
	}

}
