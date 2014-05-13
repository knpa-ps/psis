<?php

class ManagerController extends \BaseController {
	public function displayDashboard() {

		$data['mods'] = ModUser::all();

		return View::make('manager.dashboard', $data);
	}

	public function displayUserListToModify(){
		return View::make('manager.to-modify');
	}
}