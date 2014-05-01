<?php 

class PSReportService extends BaseService {

	/**
	 * $user의 입장에서 조회가능한 속보 목록에 대한 query builder를 가져온다.
	 * (필수)
	 * $params['start'] : 조회시작
	 * $params['end'] : 조회종료
	 * (옵션)
	 * $params['q'] : 제목
	 * $params['o_region'] : 지방청 작성 속보만
	 * $params['dept_id'] : 해당 관서의 하위 관서만 조회
	 * 
	 * @param User $user 
	 * @param array $params 
	 * @return Builder filtered query builder
	 */
	public function getReportListQuery($user, $params) {

		// date filtering
		$reports = PSReport::with('department', 'user', 'reads')
							->where('created_at', '>=', $params['start'])
							->where('created_at', '<=', date('Y-m-d',strtotime('+1 day', strtotime($params['end']))));

		$deptType = $user->department->type_code;
		if (!$user->isSuperUser() && $deptType != Department::TYPE_HEAD) {
			// 사용자의 관서 종류에 따라 조회 범위 설정
			if ($deptType == Department::TYPE_REGION) {
				$scopeDeptId = $user->department->region()->id;
			} else {
				$scopeDeptId = $user->department->id;
			}

			$reports->whereHas('department', function($q) use ($scopeDeptId) {
				$q->where('full_path', 'like', "%:{$scopeDeptId}:%");
			});
		}

		
		// 제목
		if (isset($params['q']) && trim($params['q'])) {
			$reports->where('title', 'like', "%{$params['q']}%");
		}

		// 지방청 작성 속보만 조회
		if (isset($params['o_region']) && $params['o_region']) {
			$reports->whereHas('department', function($q) {
				$q->where('type_code', '=', Department::TYPE_REGION);
			});
		}

		// 관서 필터링
		if (isset($params['dept_id']) && $params['dept_id']) {
			$deptId = $params['dept_id'];
			$reports->whereHas('department', function($q) use ($deptId) {
				$q->where('full_path', 'like', "%:{$deptId}:%");
			});
		}

		return $reports->orderBy('created_at', 'desc');
	}

	public function readAndGetReport($user, $reportId) {
		$report = PSReport::with('histories')->find($reportId);
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
	 * @param PSReport $report 
	 * @return array 권한
	 */
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
	 * 속보를 삭제한다. 속보 변경 내역도 모두 삭제함
	 * @param User $user 
	 * @param PSReport $report 
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
	 * 새 속보를 생성한다
	 * @param User $user creator
	 * @param string $title title
	 * @param string $content hwpctrl content string
	 * @return PSReport 생성된 속보 모델
	 */
	public function createReport($user, $title, $content) {
		DB::beginTransaction();
		$report = new PSReport;
		$report->title = $title;
		$report->creator_id = $user->id;
		$report->dept_id = $user->department->id;
		if (!$report->save()) {
			throw new Exception('db failed: report couldnt be saved');
		}

		$history = new PSReportHistory;
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
	 * @return PSReport
	 */
	public function editReport($user, $reportId, $title, $content) {
		$report = PSReport::find($reportId);
		if ($report === null) {
			throw new Exception('no report data with id='.$reportId, 400);
		}

		DB::beginTransaction();
		$report->title = $title;
		if (!$report->save()) {
			throw new Exception('db failed: report couldnt be saved');
		}

		$history = new PSReportHistory;
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
	 * 임시저장
	 * @param User $user 
	 * @param string $title 
	 * @param string $content 
	 * @return PSReportDraft
	 */
	public function saveDraft($user, $title, $content) {
		$draft = new PSReportDraft;
		$draft->user_id = $user->id;
		$draft->title = $title;
		$draft->content = $content;
		if (!$draft->save()) {
			throw new Exception('db failed', 400);
		}
		return $draft;
	}
}