<?php

class EventReportController extends BaseController {

	public function __construct() {
		$this->service = new EventReportService;
	}
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */

	public function displayDashboard() {
 		return View::make('eventreport.dashboard');
 	}
	/*
	선거 및 행사속보 작성
	*/
	public function displayComposeForm() {

		$user = Sentry::getUser();
		$data['mode'] = 'create';

		$reportId = Input::get('rid'); // 흠 rid가 들어오는 경우는 어떤 경우일까?
		if ($reportId) {
			$report = EventReport::with('histories')->find($reportId);
			if ($report === null) {
				return App::abort(400);
			}

			$permissions = $this->service->getPermissions($user, $report);
			if (!$permissions['read']) {
				return App::abort(403);
			}

			$data['report'] = $report;
		}
		$template = EventReportTemplate::where('is_default','=',1)->first();
		$data['template'] = $template;
		$data['user'] = $user;

		return View::make('eventreport.create', $data);

	}

	public function displayEditForm() {
		$user = Sentry::getUser();
		$reportId = Input::get('rid');
		if ($reportId) {
			$report = EventReport::with('histories')->find($reportId);
			if ($report === null) {
				return App::abort(400);
			}

			$permissions = $this->service->getPermissions($user, $report);
			if (!$permissions['read']) {
				return App::abort(403);
			}
		}
		$data['user'] = $user;
		$data['report'] = $report;
		$data['mode'] = 'edit';
		return View::make('eventreport.create', $data);
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
		$reportsQuery = $this->service->getReportListQuery($user, $data['input']);
		$data['total'] = $reportsQuery->count();
		$data['reports'] = $reportsQuery->paginate(15);

		$reportId = Input::get('rid');

		if ($reportId) {
			try {
				$adjacents = $this->service->getAdjacentIds($user, $reportId, $data['input']);
				if($adjacents['prev'])	{
					$data['prev_id'] = $adjacents['prev']->id;
				} else {
					$data['prev_id'] = null;
				}
				if($adjacents['next'])	{
					$data['next_id'] = $adjacents['next']->id;
				} else {
					$data['next_id'] = null;
				}
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
		return View::make('eventreport.list', $data);
	}

	public function createReport() {
		$reportType = Input::get('reportType');
		$title = Input::get('title');
		$content = Input::get('content');
		$user = Sentry::getUser();

		try {
			$report = $this->service->createReport($reportType, $user, $title, $content);  // *********
		} catch (Exception $e) {
			return App::abort($e->getCode());
		}

		return array(
				'result'=>0,
				'message'=>'제출되었습니다',
				'url' => action('EventReportController@displayList').'?rid='.$report->id
			);
	}

	public function editReport() {
		$reportType = Input::get('reportType');
		$title = Input::get('title');
		$content = Input::get('content');
		$user = Sentry::getUser();
		$reportId = Input::get('rid');

		try {
			$this->service->editReport($reportType, $user, $reportId, $title, $content);  // *********
		} catch (Exception $e) {
			return App::abort($e->getCode());
		}

		return array(
			'result'=>0,
			'message'=>'제출되었습니다',
			'url' => action('EventReportController@displayList').'?rid='.$reportId
		);
	}

	public function deleteReport() {
		$reportId = Input::get('rid');

		$user = Sentry::getUser();
		$report = EventReport::find($reportId);

		if ($report === null) {
			return App::abort(400);
		}

		try {
			$this->service->deleteReport($user, $report);  // *********
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

	public function displayDraftsList() {
		$user = Sentry::getUser();

		$drafts = EventReportDraft::where('user_id', '=', $user->id)
		->orderBy('created_at', 'desc')->paginate(10);

		$data['drafts'] = $drafts;

		return View::make('eventreport.select-drafts', $data);
	}

	public function getDraft($id) {
		return EventReportDraft::find($id);
	}

	public function saveDraft() {
		$title = Input::get('title');
		$content = Input::get('content');
		$user = Sentry::getUser();

		try {
			$this->service->saveDraft($user, $title, $content);
		} catch (Exception $e) {
			return App::abort($e->getCode(), $e->getMessage());
		}

		return array('message'=>'임시저장되었습니다.');
	}

	public function deleteDraft() {
		$id = Input::get('id');
		$user = Sentry::getUser();
		$draft = EventReportDraft::find($id);
		if (!$draft || (!$user->isSuperUser() && $draft->user_id != $user->id)) {
			return App::abort(400);
		}
		$draft->delete();
	}

	public function displayTemplatesList() {
		$data['templates'] = EventReportTemplate::all();
		return View::make('eventreport.select-templates', $data);
	}

	public function getTemplate($id) {
		return EventReportTemplate::find($id);
	}

	public function saveTemplate() {
		$template = new EventReportTemplate;
		$template->name = Input::get('title');
		$template->content = Input::get('content');
		$template->save();
		return array('message'=>'저장되었습니다');
	}

	public function deleteTemplate() {
		$id = Input::get('id');
		$template = EventReportTemplate::find($id);
		$template->delete();
	}

	public function setDefault() {
		$id = Input::get('id');
		$template = EventReportTemplate::find($id);

		//모든 양식의 is_default를 0으로 만듬
		DB::table('event_report_templates')->update(array('is_default' => 0));

		$template->is_default = 1;
		$template->save();
	}

	public function index()
	{
		//
	}


	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}


	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}


	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}


}
