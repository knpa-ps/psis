<?php

class HomeController extends BaseController {
	public function showDashboard()
	{
		$menuService = new MenuService;
		$menus = $menuService->getMenuTree();
        return View::make('main',get_defined_vars());
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