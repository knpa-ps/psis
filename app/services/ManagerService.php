<?php 

class ManagerService extends BaseService {

	function getUserListQuery($params, $user){

		//현재 로그인한 계정의 부서보다 하위 부서 필터
		$fullPath = $user->department->full_path;
		$users = User::whereHas('department', function($q) use ($fullPath) {
			$q->where('full_path','like',"{$fullPath}%");
		});

		// 사용자 그룹 필터
		// report, budget 등 선택한 부서 key 앞부분이 $params['group']에 들어있음.
		if(isset($params['group'])){
			$users->whereHas('groups', function($q) use ($params) {
				$q->where('key', 'like', "{$params['group']}%");
			});
		}
		//이건 되네 근데 조회한 이후에 셀렉트 유지되도록 하자

		//활성화 필터
		//$params['activate'] 값은 1 / 0 / all (활성화/비활성화/전체)
		if(isset($params['active'])){
			if($params['active']!=='all'){
				$users->where('activated','=',$params['active']);
			}
		}

		//수정 요청 필터
		//$params['modify_requested'] 값은 1 / all
		if(isset($params['modify_requested'])){
			if($params['modify_requested']==1){
				$users->has('modifyRequests');
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
}