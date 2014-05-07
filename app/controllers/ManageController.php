<?php

class ManageController extends \BaseController {

	public function modifyData() {
		$userId = Input::get('id');
		$user = User::find($userId);
		$modData = ModUser::where('user_id','=',$userId)->orderBy('created_at', 'desc')->first();
		
		$new = array();

		if($modData==null){
			return App::abort(400);
		}

		

		DB::transaction(function() {

			$user->user_name = $modData->user_name;
			$user->user_rank = $modData->user_rank;
			$user->dept_id = $modData->dept_id;

			$modData->approved = 1;
			$modData->save();

			if($user->save()){
				return "해당 유저의 정보가 수정되었습니다.";
			}
		});
	}
	public function getModifyData() {
		$userId = Input::get('id');
		$modData = ModUser::where('user_id','=',$userId)->orderBy('created_at', 'desc')->first();
		$new = array();
		if($modData==null){
			return 0;
		}
		$new["created_at"] = $modData->created_at;
		$new["name"] = $modData->user_name;
		$new["rank"] = Code::where('code','=',$modData->user_rank)->first()->title;
		$new["dept"] = Department::where('id','=',$modData->dept_id)->first()->full_name;

		return $new;
	}

	public function activateUser(){
		$selectedId = Input::get("selected");
		$checked = Input::get("checked");

		if($selectedId == null) {
			return "사용자가 선택되지 않았습니다.";
		}

		$user = User::find($selectedId);
		$user->activated = $checked;

		if($user->save()){
			if($checked){
				return "해당 사용자의 계정이 활성화되었습니다.";
			} else {
				return "해당 사용자의 계정이 비활성화되었습니다.";
			}
		}
	}


	public function getUserDataDetail(){
		$user = User::find(Input::get("id"));

		$data = array();
		$data["activated"] = $user->activated;
		$data["email"] = $user->email;
		$data["name"] = $user->user_name;
		$data["rank"] = $user->rank->title;
		$data["dept"] = $user->department->full_name;
		$data["createdAt"] = $user->created_at;

		return $data;

	}
	public function displayDashboard() {

		return View::make('manage.dashboard');
	}

// 현재 로그인한 사람의 부서 이하의 부서에 속하는 유저를 모두 가져와 Datatables에 출력한다.
	public function getUserData(){

		$deptId = Sentry::getUser()->dept_id;
		$dept = Department::find($deptId);

		return Datatable::query(DB::table('users')
			->join('departments', 'users.dept_id','=','departments.id')
			->where('full_path','like',"{$dept->full_path}%")
			->select('users.id','email', 'user_name', 'full_name'))
        ->showColumns('id','email', 'user_name', 'full_name')
        ->searchColumns('email','user_name')
        ->orderColumns('id')
        ->make();
	}
}