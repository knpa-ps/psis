<?php

class HomeController extends BaseController {
	public function showDashboard()
	{
        return View::make('main');
	}

	public function setConfigs()
	{
		$input = Input::all();

		$data = array();

		foreach ($input as $d)
		{
			$data[$d['name']] = $d['value'];
		}

		return PSConfig::set($data);
	}

}