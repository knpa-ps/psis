<?php 
use Carbon\Carbon;

class BgtMobPayService extends BaseService {

	public function getMasterDataQuery($user, $params) {
		$query = BgtMobPayMaster::where('use_date', '>=', $params['start'])
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

		// 행사명
		if (isset($params['event_name']) && $params['event_name']) { 
			$query->where('event_name', 'like', "%{$params['event_name']}%");
		}

		return $query;
	}

	public function insert($user, $input) {
		DB::beginTransaction();
		$master = new BgtMobPayMaster;
		$master->creator_id = $user->id;
		$master->dept_id = $user->dept_id;
		$master->use_date = $input['use_date'];
		$master->sit_code = $input['sit_code'];
		$master->event_name = $input['event_name'];

		if (!$master->save()) {
			throw new Exception('db failed', 500);
		}

		foreach ($input['details'] as $d) {
			$detail = new BgtMobPayDetail;
			$detail->master_id = $master->id;
			$detail->dept_id = $d['dept_id'];
			$detail->rank_code = $d['rank'];
			$detail->name = $d['name'];
			$detail->start = $d['start_date'].' '.$d['start_time'];
			$detail->end = $d['end_date'].' '.$d['end_time'];
			$detail->amount = $d['amount'];

			if (!$detail->save()) {
				throw new Exception('db failed', 500);
			}			
		}

		DB::commit();
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

	public function getPermissions(User $user, BgtMobPayMaster $data) {
		$scope = $this->getScopeDept($user);
		$permissions['read'] = $scope==null?1:($scope->isAncestor($data->dept_id)?1:0);
		$permissions['update'] = $permissions['read'];
		$permissions['delete'] = $permissions['read'];
		return $permissions;
	}

	public function delete($user, $id) {
		$master = BgtMobPayMaster::find($id);
		if ($master === null) {
			throw new Exception('not found', 404);
		}

		$permissions = $this->getPermissions($user, $master);
		if (!$permissions['delete']) {
			throw new Exception('no permission', 403);
		}

		DB::beginTransaction();
		$master->details()->delete();
		$master->delete();
		DB::commit();
	}

	public function update($user, $masterId, $input) {
		$master = BgtMobPayMaster::find($masterId);
		if ($master == null) {
			throw new Exception('not found', 404);
		}

		$permissions = $this->getPermissions($user, $master);
		if (!$permissions['update']) {
			throw new Exception('no permission', 403);
		}

		DB::beginTransaction();
		$master->use_date = $input['use_date'];
		$master->sit_code = $input['sit_code'];
		$master->event_name = $input['event_name'];

		if (!$master->save()) {
			throw new Exception('db failed', 500);
		}

		$remainIds = array();
		foreach ($input['details'] as $d) {
			
			if ($d['id']) {
				$detail = BgtMobPayDetail::find($d['id']);
				if ($detail === null) {
					continue;
				}
			} else {
				$detail = new BgtMobPayDetail;
			}

			$detail->master_id = $master->id;
			$detail->dept_id = $d['dept_id'];
			$detail->rank_code = $d['rank'];
			$detail->name = $d['name'];
			$detail->start = $d['start_date'].' '.$d['start_time'];
			$detail->end = $d['end_date'].' '.$d['end_time'];
			$detail->amount = $d['amount'];

			if (!$detail->save()) {
				throw new Exception('db failed', 500);
			}

			$remainIds[] = $detail->id;
		}

		// 삭제된 상세내역 지우기
		BgtMobPayDetail::where('master_id', '=', $master->id)
						->whereNotIn('id', $remainIds)->delete();

		DB::commit();

	}
}