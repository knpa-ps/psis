<?php 

class PSReportService extends BaseService {

	public function getReportListQuery($params, $rowCount = 15) {

		// date filtering
		$reports = PSReport::with('department', 'user')->where('created_at', '>=', $params['start'])
							->where('created_at', '<=', date('Y-m-d',strtotime('+1 day', strtotime($params['end']))));

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

		return $reports->paginate($rowCount);
	}

}