<?php
class EventReportService extends BaseService {

  private function getScopeDept($user) {
		$deptType = $user->department->type_code;
    // 사용자의 관서 종류에 따라 조회 범위 설정
    if (!$user->isSuperUser() && $deptType != Department::TYPE_HEAD) { //본청 또는 관리자가 아니면
			if ($deptType == Department::TYPE_REGION) { //지방청이면
				return $user->department->region(); //지방청 id를 반환
			} else {
				return $user->department; //그 이외인 경우 부서 full_path를 반환
			}
		}
		return null; //본청이거나 관리자면 null
	}
  public function getReportListQuery($user, $params) {

    // date filtering
    $reports = EventReport::with('department', 'user')
              ->where('created_at', '>=', $params['start'])
              ->where('created_at', '<=', date('Y-m-d',strtotime('+1 day', strtotime($params['end']))));

    $scope = $this->getScopeDept($user);
    if ($scope) {

      $reports->whereHas('user', function($q) use ($scope) {
      	$q->whereHas('department', function($qq) use ($scope) {
      		$qq->where('full_path', 'like', "{$scope->full_path}%");
      	});
      });
    }

    // 제목
    if (isset($params['q']) && trim($params['q'])) {
      $reports->where('title', 'like', "%{$params['q']}%");
    }

    // 지방청 작성 속보만 조회
    if (isset($params['o_region']) && $params['o_region']) {
      $reports->whereHas('user', function ($q) {
      	$q->whereHas('department', function($qq) {
      		$qq->where('type_code', '=', Department::TYPE_REGION);
      	});
      });
    }

    // 보고서유형 필터링
    if (isset($params['report_type'])) {
      $reports->whereIn('report_type',$params['report_type']);
    }

    // 관서 필터링
    if (isset($params['dept_id']) && $params['dept_id']) {
      $deptId = $params['dept_id'];
      $reports->whereHas('user', function($q) use ($deptId) {
      	$q->whereHas('department', function($qq) use ($deptId) {
        	$qq->where('full_path', 'like', "%:{$deptId}:%");
        });
      });
    }

    return $reports->orderBy('created_at', 'desc');
  }

  public function getAdjacentIds($user, $reportId, $params) {
		$current = EventReport::find($reportId);
		// date filtering
		$reports = EventReport::with('department', 'user', 'reads')
							->where('created_at', '>=', $params['start'])
							->where('created_at', '<=', date('Y-m-d',strtotime('+1 day', strtotime($params['end']))));

		$scope = $this->getScopeDept($user);
	    if ($scope) {

	      $reports->whereHas('user', function($q) use ($scope) {
	      	$q->whereHas('department', function($qq) use ($scope) {
	      		$qq->where('full_path', 'like', "{$scope->full_path}%");
	      	});
	      });
	    }

	    // 제목
	    if (isset($params['q']) && trim($params['q'])) {
	      $reports->where('title', 'like', "%{$params['q']}%");
	    }

	    // 지방청 작성 속보만 조회
	    if (isset($params['o_region']) && $params['o_region']) {
	      $reports->whereHas('user', function ($q) {
	      	$q->whereHas('department', function($qq) {
	      		$qq->where('type_code', '=', Department::TYPE_REGION);
	      	});
	      });
	    }

	    // 보고서유형 필터링
	    if (isset($params['report_type'])) {
	      $reports->whereIn('report_type',$params['report_type']);
	    }

	    // 관서 필터링
	    if (isset($params['dept_id']) && $params['dept_id']) {
	      $deptId = $params['dept_id'];
	      $reports->whereHas('user', function($q) use ($deptId) {
	      	$q->whereHas('department', function($qq) use ($deptId) {
	        	$qq->where('full_path', 'like', "%:{$deptId}:%");
	        });
	      });
	    }

		$nextQuery = clone $reports;
		$next = $nextQuery->where('id', '>', $current->id)->orderBy('id', 'asc')->first();
	 	$prevQuery = clone $reports;
	 	$prev = $prevQuery->where('id', '<', $current->id)->orderBy('id', 'desc')->first();

	 	return compact('prev', 'next');
	}

  public function readAndGetReport($user, $reportId) {
		$report = EventReport::with('histories')->find($reportId);
		if ($report === null) {
			throw new Exception('no report data found with id='.$reportId, 404);
		}

		// set read
		if (!$report->has_read) {
			$report->readers()->attach($user->id);
		}

		return $report;
	}

  /**
	 * $user가 $report에 대해 갖고 있는 권한을 가져온다.
	 * $permissions['read'] : 읽기
	 * $permissions['update'] : 수정
	 * $permissions['delete'] : 삭제
	 *
	 * @param User $user
	 * @param EventReport $report
	 * @return array 권한
	**/
	public function getPermissions($user, $report) {
		// super user 이면 걍 다 ㅇㅋ
		// 자기가 쓴거면 조회 수정,삭제 가능
		if ($user->isSuperUser() || $report->user->id == $user->id) {
			return array(
					'read'=>1,
					'update'=>1,
					'delete'=>1
				);
		}

		// 부서 타입에 따라 조회 권한 따지기
		switch ($user->department->type_code) {
			case Department::TYPE_HEAD:
				// 본청은 다 볼 수 있음
				$readable = 1;
			break;
			case Department::TYPE_REGION:
				// 지방청이면 해당 지방청의 모든 속보 조회 가능
				$readable = $user->department->region()->isAncestor($report->dept_id)?1:0;
			break;
			case Department::TYPE_OFFICE:
				// 일반 관서면 자기 부서 또는 하위부서가 쓴 것들에 대해 조회 가능
				$readable = $user->department->isAncestor($report->dept_id)?1:0;
			break;
			default:
				$readable = 0;
			break;
		}

		return array(
					'read'=>$readable,
					'update'=>0,
					'delete'=>0
				);
	}

  /**
	 * 새 속보를 생성한다
	 * @param User $user creator
	 * @param string $title title
	 * @param string $content hwpctrl content string
	 * @return EventReport 생성된 속보 모델
	 */
	public function createReport($reportType, $user, $title, $content) {
		DB::beginTransaction();
		$report = new EventReport;
		$report->title = $title;
		$report->creator_id = $user->id;
		$report->dept_id = $user->department->id;
    $report->report_type = $reportType;
		if (!$report->save()) {
			throw new Exception('db failed: report couldnt be saved');
		}

		$history = new EventReportHistory;
		$history->report_id = $report->id;
		$history->creator_id = $user->id;
		$history->content = $content;

		if (!$history->save()) {
			throw new Exception('db failed: history cannot be saved');
		}

		DB::commit();

		return $report;
	}

  /**
	 * 속보를 수정하여 history를 추가한다.
	 * @param User $user creator
	 * @param int $reportId repot id
	 * @param string $title
	 * @param string $content
	 * @return EventReport
	 */
	public function editReport($reportType, $user, $reportId, $title, $content) {
		$report = EventReport::find($reportId);
		if ($report === null) {
			throw new Exception('no report data with id='.$reportId, 400);
		}

		DB::beginTransaction();
		$report->title = $title;
    $report->report_type = $reportType;

		if (!$report->save()) {
			throw new Exception('db failed: report couldnt be saved');
		}

		$history = new EventReportHistory;
		$history->report_id = $report->id;
		$history->creator_id = $user->id;
		$history->content = $content;
		if (!$history->save()) {
			throw new Exception('db failed: history cannot be saved');
		}

		DB::commit();

		return $report;
	}

  /**
	 * 속보를 삭제한다. 속보 변경 내역도 모두 삭제함
	 * @param User $user
	 * @param EventReport $report
	 */
	public function deleteReport($user, $report) {
		$permissions = $this->getPermissions($user, $report);
		if (!$permissions['delete']) {
			throw new Exception('does not have permission', -1);
		}

		DB::beginTransaction();
		$report->histories()->delete();
		$report->delete();
		DB::commit();
	}

  /**
	 * 임시저장
	 * @param User $user
	 * @param string $title
	 * @param string $content
	 * @return EventReportDraft
	 */
	public function saveDraft($user, $title, $content) {
		$draft = new EventReportDraft;
		$draft->user_id = $user->id;
		$draft->title = $title;
		$draft->content = $content;
		if (!$draft->save()) {
			throw new Exception('db failed', 400);
		}
		return $draft;
	}
}
