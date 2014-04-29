<?php

class HomeController extends BaseController {

	public function displayDashboard() {
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

	public function migrateV2() {
		$user = Sentry::getUser();

		if (DB::table('user_migrations')->where('user_id', '=', $user->id)->count()>0){
			return Redirect::to('/');
		}

		$form = Input::all();
		if (isset($form['dept_id'])) {

			DB::beginTransaction();
			DB::table('users_groups')->where('user_id','=',$user->id)->delete();
			$user->dept_id = $form['dept_id'];
			$user->save();

			// 시스템 사용 신청에 따라 사용자 권한 부여
			foreach ($form['groups'] as $system => $group) {

				if ($group == 'none') {
					continue;
				}

				$groupKey = $system.'.'.$group;

				$group = Group::ofKey($groupKey)->first();

				if ($group != null) {
					$user->addGroup($group);
				}

			}

			DB::table('user_migrations')->insert(array(
				'user_id'=>$user->id,
				'migrated_at'=>date('Y-m-d h:i:s')
				));

			DB::commit();
			Session::flash('message', '완료되었습니다.');
			return Redirect::to('/');
		}

		return View::make('migrate', array('user'=>$user));
	}
}