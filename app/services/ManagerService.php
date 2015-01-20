<?php 

class ManagerService extends BaseService {

	public function getModifyRequestsList($fullPath){
		return ModUser::whereHas('user', function($q) use($fullPath) {
			$q->whereHas('department',function($q2) use($fullPath) {
				$q2->where('full_path','like',$fullPath.'%');
			});
		})->where('approved','=','0')->orderBy('id','desc')->get();
	}
	public function getFilteredUserListQuery($params){

		// 사용자 그룹 필터
		// report, budget 등 선택한 부서 key 앞부분이 $params['group']에 들어있음.
		if(isset($params['group'])){
			if ($params['group']=='all') {
				$users = User::where('id','like','%');
			} else {
				$users = User::whereHas('groups', function($q) use ($params) {
					$q->where('key', 'like', "{$params['group']}%");
				});	
			}
		}
		//조회한 이후에 셀렉트 유지되도록 수정필요

		//활성화 필터
		//$params['activate'] 값은 1 / 0 / all (활성화/비활성화/전체)
		if(isset($params['active'])){
			if($params['active']!=='all'){
				$users->where('activated','=',$params['active']);
			}
		}
		
		//이름, 계정명 필터
		//$params['account_name']에 string으로 들어옴
		if(isset($params['account_name'])){
			if($params['account_name']!==''){
				$users->where('user_name','like','%'.$params['account_name'].'%')->orWhere('email','like','%'.$params['account_name'].'%');
			}
		}

		return $users;

	}

	public function getLowerUserListQuery($users){

		$user = Sentry::getUser();
		//현재 로그인한 계정의 부서보다 하위 부서 필터
		$fullPath = $user->department->full_path;

		$users->whereHas('department', function($q) use ($fullPath) {
			$q->where('full_path','like',"{$fullPath}%");
		});

		return $users;
	}

	public function register($form) {

		// 유저 생성
		try {
			
			$user = Sentry::createUser(array(
					'activated' => $form['status'],
					'account_name' => $form['account_name'],
					'email' => $form['account_name'],
					'password' => $form['password'],
					'user_name' => $form['user_name'],
					'dept_id' => $form['dept_id'],
					'user_rank' => $form['user_rank'],
					'contact' => $form['contact'],
					'contact_phone' => $form['contact_phone'],
					'contact_extension' => $form['contact_extension']
				));

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

		} catch (Cartalyst\Sentry\Users\UserExistsException $e) {
		    throw new Exception('account name already exists', -1);
		}

	}
}