<?php

class ManageController extends \BaseController {

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
			->select('email', 'user_name', 'full_name'))
        ->showColumns('email', 'user_name', 'full_name')
        ->searchColumns('email','user_name')
        ->orderColumns('full_name')
        ->make();
	}
}