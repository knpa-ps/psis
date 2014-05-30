<?php

class ManagerController extends \BaseController {

	public function __construct() {
		$this->service = new ManagerService;
	}

	public function displayDashboard() {

		$user = Sentry::getUser();
		$fullPath = $user->department->full_path;

		$data['mods'] = $this->service->getModifyRequestsList($fullPath);

		return View::make('manager.dashboard', $data);
	}

	public function displayUserListToModify(){

		$user = Sentry::getUser();
		$fullPath = $user->department->full_path;
		
		$data['mods'] = $this->service->getModifyRequestsList($fullPath);

		return View::make('manager.to-modify', $data);
	}

	public function showModified($id){

		$new = ModUser::find($id);
		$user = User::find($new->user_id);

		$data = array();
		$data['newrank'] = $new->rank->title;
		$data['newdept'] = $new->department->full_name;
		$data['newname'] = $new->user_name;
		$data['oldrank'] = $user->rank->title;
		$data['oldname'] = $user->user_name;
		$data['olddept'] = $user->department->full_name;

		return $data;
	}

	public function saveModified($id){

		$new = ModUser::find($id);
		$user = User::find($new->user_id);

		$user->user_rank = $new->user_rank;
		$user->user_name = $new->user_name;
		$user->dept_id = $new->dept_id;

		if($user->save()){
			DB::update('update mod_user set approved = 1 where user_id ='.$new->user_id);
			return "정보가 수정되었습니다.";
		} else {
			return "서버 오류가 발생했습니다.";
		}
	}
}