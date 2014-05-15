<?php

class ManagerController extends \BaseController {
	public function displayDashboard() {

		$user = Sentry::getUser();
		$fullPath = $user->department->full_path;
		$data['mods'] = ModUser::whereHas('user', function($q) use($fullPath) {
			$q->whereHas('department',function($q2) use($fullPath) {
				$q2->where('full_path','like',$fullPath.'%');
			});
		})->orderBy('id','desc')->get();

		return View::make('manager.dashboard', $data);
	}

	public function displayUserListToModify(){

		$user = Sentry::getUser();
		$fullPath = $user->department->full_path;
		$data['mods'] = ModUser::whereHas('user', function($q) use($fullPath) {
			$q->whereHas('department',function($q2) use($fullPath) {
				$q2->where('full_path','like',$fullPath.'%');
			});
		})->orderBy('id','desc')->get();

		return View::make('manager.to-modify', $data);
	}
}