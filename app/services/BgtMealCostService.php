<?php 

class BgtMealCostService extends BaseService {

	public function insert($user, $data) {
		DB::beginTransaction();
		foreach ($data as $d) {
			$cost = new BgtMealCost;
			$cost->dept_id = $user->dept_id;
			$cost->creator_id = $user->id;
			$cost->use_date = $d['use_date'];
			$cost->use_code = $d['use_code'];
			$cost->sit_code = $d['sit_code'];
			$cost->event_name = $d['event_name'];
			$cost->meal_count = $d['meal_count'];
			$cost->meal_amount = $d['meal_amount'];
			$cost->save();
		}
		DB::commit();
	}	

	public function update($id, $data) {
		DB::beginTransaction();
			$cost = BgtMealCost::find($id);
			$cost->use_date = $data['use_date'];
			$cost->use_code = $data['use_code'];
			$cost->sit_code = $data['sit_code'];
			$cost->event_name = $data['event_name'];
			$cost->meal_count = $data['meal_count'];
			$cost->meal_amount = $data['meal_amount'];
			$cost->save();
		DB::commit();
	}	

	public function getDataQuery($user, $params) {

		$query = BgtMealCost::where('use_date', '>=', $params['start'])
							->where('use_date', '<=', $params['end'])
							->orderBy('use_date', 'desc')
							->orderBy('dept_id', 'asc');

		// 부서 종류에 따라 조회 범위 제한
		$scope = $this->getScopeDept($user);
		if ($scope != null) {
			$query->whereHas('department', function($q) use ($scope) {
				$q->where('full_path', 'like', "{$scope->full_path}%");
			});
		}

		// 관서 필터링
		if (isset($params['dept_id']) && $params['dept_id']) {
			$filterDept = Department::find($params['dept_id']);
			if ($filterDept) {
				$query->whereHas('department', function($q) use ($filterDept) {
					$q->where('full_path', 'like', "{$filterDept->full_path}%");
				});
			}
		}

		// 동원상황구분
		if (isset($params['sit_code']) && $params['sit_code']) { 
			$query->where('sit_code', '=', $params['sit_code']);
		}

		if (isset($params['use_code']) && $params['use_code']) { 
			$query->where('use_code', '=', $params['use_code']);
		}

		// 행사명
		if (isset($params['event_name']) && $params['event_name']) { 
			$query->where('event_name', 'like', "%{$params['event_name']}%");
		}

		return $query;
	}

	public function getScopeDept($user) {
		if (!$user->isSuperUser() && $user->department->type_code != Department::TYPE_HEAD) {
			// 사용자의 관서 종류에 따라 조회 범위 설정
			if ($user->department->type_code == Department::TYPE_REGION) {
				$scopeRootDept = $user->department->region();
			} else {
				$scopeRootDept = $user->department;
			}
			return $scopeRootDept;
		} else {
			return null;
		}
	}

	public function getPermissions(User $user, BgtMealCost $data) {
		$scope = $this->getScopeDept($user);
		$permissions['read'] = $scope==null?1:($scope->isAncestor($data->dept_id)?1:0);
		$permissions['update'] = $permissions['read'];
		$permissions['delete'] = $permissions['read'];
		return $permissions;
	}
}