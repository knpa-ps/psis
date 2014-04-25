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

	public function showComposeForm()
	{
		$user = Sentry::getUser();
		if (!$user->hasAccess('reports.create')) {
			return App::abort(403, 'unauthorized action');
		}

		return View::make('report.compose');
	}

	public function showList()
	{
		$user = Sentry::getUser();
		if (!$user->hasAccess('reports.read')) {
			return App::abort(403, 'unauthorized action');
		}

		return View::make('report.list', array('user'=>$user));
	}

	public function uploadAttachments()
	{
		$user = Sentry::getUser();
		if (!$user->hasAccess('reports.create')) {
			return App::abort(403, 'unauthorized action');
		}

		if (!empty($_FILES)) {
			$tempFile = $_FILES['Filedata']['tmp_name'];
			$targetPath = Config::get('app.uploadPath');
			$fileParts = pathinfo($_FILES['Filedata']['name']);
			$ext = strtolower($fileParts['extension']);
			$targetFile = rtrim($targetPath,'/') . '/' . hash('sha256', $_FILES['Filedata']['name']).'.'.$ext;
			$fileOriginalName = Input::get('Filename');
			// Validate the file type
			$fileTypes = array('jpg','jpeg','gif','png', 'hwp', 'xls', 'xlsx', 'cell'); // File extensions
			if (in_array($ext,$fileTypes)) {
				if (move_uploaded_file($tempFile,$targetFile)) {
					$psFile = new PSFile;
					$psFile->name = $fileOriginalName;
					$psFile->path = $targetFile;
					$psFile->ext = $ext;
					$psFile->size = $_FILES['Filedata']['size'];
					$psFile->uploader_id = Sentry::getUser()->id;
					$psFile->save();
					return $psFile->id;
				} else {
					return App::abort(400, 'failed to move files to upload path');
				}
			} else {
				return App::abort(400, 'file extension not allowed');
			}
		}

		return App::abort(400, 'file not uploaded');
	}

	public function insertReport()
	{
		$user = Sentry::getUser();
		if (!$user->hasAccess('reports.create')) {
			return App::abort(403, 'unauthorized action');
		}

		$title = Input::get('report-title');
		$content = Input::get('report-content');
		$files = Input::get('files');
	    
        if ($files) {    
    		$fileIds = implode(',', $files);
        } else {
            $fileIds = '';
        }
		$user = Sentry::getUser();
		$report = new PSReport;
		$report->title = $title;
		$report->closed = 0;
		$report->creator_id = $user->id;
		$report->save();

		$history = new PSReportHistory;
		$history->report_id = $report->id;
		$history->creator_id = $user->id;
		$history->content = $content;
		$history->file_ids = $fileIds;
		$history->save();

		return 0;
	}

	public function getReports() 
	{
		$user = Sentry::getUser();
		if (!$user->hasAccess('reports.read')) {
			return App::abort(403, 'unauthorized action');
		}

		$query = DB::table('ps_reports')
			->leftJoin('users', 'users.id', '=', 'ps_reports.creator_id')
			->leftJoin('departments', 'departments.id', '=', 'users.dept_id')
			->select(array('ps_reports.id', 'title', 'closed', 'ps_reports.created_at', 'departments.full_name',
				'users.user_name'))->orderBy('ps_reports.created_at', 'desc');

		$start = Input::get('q_date_start');

		if ($start)
		{
			$query->where('ps_reports.created_at', '>=', $start);
		}

		$end = Input::get('q_date_end');

		if ($end)
		{
			$query->where('ps_reports.created_at', '<=', $end);
		}

		$title = Input::get('q_title');

		if ($title)
		{
			$query->where('ps_reports.title', 'like', "%$title%");
		}

		$dept = Input::get('q_department');

		if ($dept)
		{
			$query->where('departments.full_name', 'like', "%$dept%");
		}

		if (!$user->hasAccess('reports.admin')) 
		{
			$query->where('departments.full_path', 'like', '%'.$user->dept_id.'%');
		}

		if (Input::get('mine'))
		{
			$query->where('ps_reports.creator_id', $user->id);
		}

		if (Input::get('q_region')) {
			$query->where('departments.depth', '=', 1);
		}

		return Datatables::of($query)->make();
	}

	public function showReport()
	{
		$user = Sentry::getUser();
		if (!$user->hasAccess('reports.read')) {
			return App::abort(403, 'unauthorized action');
		}

		$reportId = Input::get('id');
		$historyId = Input::get('hid');

		$report = PSReport::where('id','=',$reportId)->with('user', 'user.department')->first();

		$currentUser = Sentry::getUser();
		if (!$currentUser->hasAccess('reports.admin')) 
		{
			$fullPath = $report->user->department->full_path;
			if (strstr($fullPath, ":{$currentUser->dept_id}:") === FALSE)
			{
				return App::abort(403, 'unauthorized action');
			}
		}

		$reportData = null;
		if ($historyId)
		{
			$reportData = $report->histories()->where('id', '=', $historyId)->first();
		}

		if (!$reportData)
		{
			$reportData = $report->histories()->orderBy('created_at', 'desc')->take(1)->first();
		}

		if ($reportData->file_ids)
		{
			$fileIds = explode(',', $reportData->file_ids);
			$files = PSFile::whereIn('id',$fileIds)->get();
		}
		else
		{
			$files = array();
		}

		$histories = $report->histories()->orderBy('created_at','desc')->get();

		return View::make('report.detail', array(
			'report'=>$report, 'reportData'=>$reportData, 
			'files'=>$files,
			'histories'=>$histories,
			'user'=>$user));
	}

	public function setClosed()
	{
		$user = Sentry::getUser();
		if (!$user->hasAccess('reports.close')) {
			return App::abort(403, 'unauthorized action');
		}

		$closed = Input::get('closed');
		$ids = Input::get('ids');
		foreach ($ids as $id)
		{
			if (!is_numeric($id))
			{
				Log::error('requested id is not numeric value');
				return Lang::get('strings.server_error');
			}
		}
		
		PSReport::whereIn('id', $ids)->update(array('closed'=>$closed));
	}

	public function editReport()
	{
		$user = Sentry::getUser();
		if (!$user->hasAccess('reports.update')) {
			return App::abort(403, 'unauthorized action');
		}

		$content = Input::get('content');
		$files = Input::get('files')?Input::get('files'): array();

		$rid = Input::get('rid');

		$report = PSReport::where('id','=',$rid)->with('user', 'user.department')->first();

		if (!$user->hasAccess('admin')) 
		{
			$fullPath = $report->user->department->full_path;
			if (strstr($fullPath, ":{$user->dept_id}:") === FALSE)
			{
				return App::abort(403, 'unauthorized action');
			}
		}

		if ($report->closed) {
			return App::abort(400, "closed report");
		}

		$history = new PSReportHistory;
		$history->report_id = $report->id;
		$history->creator_id = $user->id;
		$history->content = $content;
		$history->file_ids = implode(',', $files);

		$history->save();
		$report->touch();
		return $history->id;
	}

	public function showMyReports() 
	{
		$user = Sentry::getUser();
		if (!$user->hasAccess('reports.read')) {
			return App::abort(403, 'unauthorized action');
		}

		return View::make('report.my', get_defined_vars());
	}

	public function showStats() 
	{
		$user = Sentry::getUser();
		if (!$user->hasAccess('reports.admin')) {
			return App::abort(403, 'unauthorized action');
		}

		return View::make('report.stats', get_defined_vars());
	}

	public function getUserStats()
	{
		$start = Input::get('q_date_start');
		$end = Input::get('q_date_end');
		$deptId = Input::get('q_dept_id');

		$query = DB::table('ps_reports')
					->leftJoin('users','users.id','=','ps_reports.creator_id')
					->leftJoin('departments','departments.id','=','users.dept_id')
					->leftJoin('codes AS rank', function($query){
						$query->on('rank.code','=','users.user_rank')
							->where('rank.category_code', '=', 'H001');
					})
					->groupBy('creator_id')
					->select(array(
							DB::raw('TRIM(REPLACE(full_name,":"," ")) as dept_name'),
							'rank.title',
							'users.user_name',
							DB::raw('COUNT(*) as report_count')
						))
					->where('ps_reports.created_at', '>=', $start)
					->where('ps_reports.created_at', '<=', $end);

		if ($deptId)
		{
			$query->where('departments.full_path','like',"%:$deptId:%");
		}

		if (!Sentry::getUser()->hasAccess('reports.admin'))
		{
			$query->where('departments.full_path','like',"%:".Sentry::getUser()->dept_id.":%");
		}

		return Datatables::of($query)->make();
	}

	public function getDeptStats()
	{
		$start = Input::get('q_date_start');
		$end = Input::get('q_date_end');
		$deptId = Input::get('q_dept_id');

		$query = DB::table('ps_reports')
					->leftJoin('users','users.id','=','ps_reports.creator_id')
					->leftJoin('departments','departments.id','=','users.dept_id')
					->groupBy('users.dept_id')
					->select(array(
							DB::raw('TRIM(REPLACE(full_name,":"," ")) as dept_name'),
							DB::raw('COUNT(*) as report_count')
						))
					->where('ps_reports.created_at', '>=', $start)
					->where('ps_reports.created_at', '<=', $end);

		if ($deptId)
		{
			$query->where('departments.full_path','like',"%:$deptId:%");
		}

		if (!Sentry::getUser()->hasAccess('reports.admin'))
		{
			$query->where('departments.full_path','like',"%:".Sentry::getUser()->dept_id.":%");
		}

		return Datatables::of($query)->make();

	}

	public function copyReport() {

		$user = Sentry::getUser();
		if (!$user->hasAccess('reports.create')) {
			return App::abort(403, 'unauthorized action');
		}

		$id = Input::get('id');
		$hid = Input::get('hid');

		$user = Sentry::getUser();
		if (!$user->hasAccess('reports.create')) {
			return App::abort(403, 'unauthorized action');
		}
		$report = PSReport::where('id','=',$id)->with('user', 'user.department')->first();

		$currentUser = Sentry::getUser();
		if (!$currentUser->hasAccess('reports.admin')) 
		{
			$fullPath = $report->user->department->full_path;
			if (strstr($fullPath, ":{$currentUser->dept_id}:") === FALSE)
			{
				return App::abort(403, 'unauthorized action');
			}
		}

		$reportData = $report->histories()->where('id', '=', $hid)->first();
		if (!$reportData) {
			return App::abort(404, 'page not found');
		}
		return View::make('report.compose', get_defined_vars());
	}
}
