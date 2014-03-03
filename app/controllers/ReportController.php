<?php

class ReportController extends BaseController {

	public function showComposeForm()
	{
		$user = Sentry::getUser();
		if (!$user->hasAccess('reports.create')) {
			return App::abort(404, 'unauthorized action');
		}

		return View::make('report.compose');
	}

	public function showList()
	{
		$user = Sentry::getUser();
		if (!$user->hasAccess('reports.read')) {
			return App::abort(404, 'unauthorized action');
		}

		return View::make('report.list', array('user'=>$user));
	}

	public function uploadAttachments()
	{
		$user = Sentry::getUser();
		if (!$user->hasAccess('reports.create')) {
			return App::abort(404, 'unauthorized action');
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
			return App::abort(404, 'unauthorized action');
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
			return App::abort(404, 'unauthorized action');
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

		return Datatables::of($query)->make();
	}

	public function showReport()
	{
		$user = Sentry::getUser();
		if (!$user->hasAccess('reports.read')) {
			return App::abort(404, 'unauthorized action');
		}

		$reportId = Input::get('id');
		$historyId = Input::get('hid');

		$report = PSReport::where('id','=',$reportId)->with('user', 'user.department')->first();

		$currentUser = Sentry::getUser();
		if (!$currentUser->isSuperUser()) 
		{
			$fullPath = $report->user->department->full_path;
			if (strstr($fullPath, ":{$currentUser->dept_id}:") === FALSE)
			{
				return App::abort(404, 'unauthorized action');
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
			return App::abort(404, 'unauthorized action');
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
			return App::abort(404, 'unauthorized action');
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
				return App::abort(404, 'unauthorized action');
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
			return App::abort(404, 'unauthorized action');
		}

		return View::make('report.my', get_defined_vars());
	}

	public function showStats() 
	{
		$user = Sentry::getUser();
		if (!$user->hasAccess('reports.admin')) {
			return App::abort(404, 'unauthorized action');
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
}
