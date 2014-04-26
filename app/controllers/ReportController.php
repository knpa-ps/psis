<?php

class ReportController extends BaseController {

	private $service;

	public function __construct() {
		$this->service = new PSReportService;
	}

	/**
	 * 대쉬보드를 구현하기 위해 일단 만들어 놨는데 아직 대쉬보드에 뭐가 들어가야 할지 몰라서
	 * 그냥 경비속보 조회 목록으로 Redirect 함
	 */
	public function displayDashboard() {
		return Redirect::action('ReportController@displayList');
	}

	public function displayComposeForm() {

		$user = Sentry::getUser();
		$data['mode'] = 'create';

		$reportId = Input::get('rid');
		if ($reportId) {
			$report = PSReport::with('histories')->find($reportId);
			if ($report === null) {
				return App::abort(400);
			}

			$permissions = $this->service->getPermissions($user, $report);
			if (!$permissions['read']) {
				return App::abort(403);
			}
		
			$data['report'] = $report;
		} 

		return View::make('report.create', $data);
	}

	public function displayEditForm() {
		$user = Sentry::getUser();
		$reportId = Input::get('rid');
		if ($reportId) {
			$report = PSReport::with('histories')->find($reportId);
			if ($report === null) {
				return App::abort(400);
			}

			$permissions = $this->service->getPermissions($user, $report);
			if (!$permissions['read']) {
				return App::abort(403);
			}
		}
		
		$data['report'] = $report;
		$data['mode'] = 'edit';
		return View::make('report.create', $data);
	}

	public function displayList() {

		$user = Sentry::getUser();

		$data['input'] = Input::all();
		// page는 laravel pagination에서 알아서 핸들하도록 unset
		unset($data['input']['page']);

		// start, end 가 설정되지 않았거나 start가 end보다 이후인 경우, 또는 날짜가 이상해서
		// strtotime으로 인식이 안되는 경우 기본적으로 오늘~한달 전으로 조회 날짜를 설정한다
		if (!Input::get('start') || !Input::get('end') 
			|| strtotime(Input::get('start')) > strtotime(Input::get('end'))) {
			$data['input']['start'] = date('Y-m-d', strtotime('-1 month'));
			$data['input']['end'] = date('Y-m-d');
		}

		// report list 가져오기
		$reportsQuery = $this->service->getReportListQuery(User::find(211), $data['input']);
		$data['total'] = $reportsQuery->count();
		$data['reports'] = $reportsQuery->paginate(15);

		$reportId = Input::get('rid');
		if ($reportId) {
			try {
				$data['report'] = $this->service->readAndGetReport($user, $reportId);
				$data['permissions'] = $this->service->getPermissions($user, $data['report']);

				// 읽기 권한이 없으면 403
				if (!$data['permissions']['read']) {
					return App::abort(403);
				}

			} catch (Exception $e) {
				return App::abort($e->getCode());
			}

		}

		return View::make('report.list', $data);
	}

	public function createReport() {
		$title = Input::get('title');
		$content = Input::get('content');
		$user = Sentry::getUser();

		try {
			$report = $this->service->createReport($user, $title, $content);
		} catch (Exception $e) {
			return App::abort($e->getCode());
		}

		return array(
				'result'=>0,
				'message'=>'제출되었습니다',
				'url' => action('ReportController@displayList').'?rid='.$report->id
			);
	}

	public function editReport() {
		$title = Input::get('title');
		$content = Input::get('content');
		$user = Sentry::getUser();
		$reportId = Input::get('rid');

		try {
			$this->service->editReport($user, $reportId, $title, $content);
		} catch (Exception $e) {
			return App::abort($e->getCode());
		}

		return array(
			'result'=>0,
			'message'=>'제출되었습니다',
			'url' => action('ReportController@displayList').'?rid='.$report->id
		);	
	}

	public function deleteReport() {
		$reportId = Input::get('rid');

		$user = Sentry::getUser();
		$report = PSReport::find($reportId);

		if ($report === null) {
			return App::abort(400);
		}

		try {
			$this->service->deleteReport($user, $report);
		} catch (Exception $e) {
			if ($e->getCode() == -1) {
				return array('result' => -1, 'message'=>'해당 속보를 삭제할 권한이 없습니다');
			} else {
				return App::abort($e->getCode());
			}
		}

		return array(
				'result' => 0,
				'message' => '삭제되었습니다'
		);
	}
}
