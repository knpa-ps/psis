<?php

class AdminController extends BaseController {

	public function showGroupList()
	{
		return View::make('main');
	}

	public function showUserList($data = array())
	{
        $codes = Code::in('H001');
        $codeSelectItems = array();
        foreach ($codes as $code) {
            $codeSelectItems[$code->code] = $code->title;
        }
        $data['codeSelectItems'] = $codeSelectItems;

        if (Input::all()) {
	        $data['accountName'] = Input::get('account_name');
	        $data['password'] = Input::get('password');
	        $data['passwordConf'] = Input::get('password_confirmation');
	        $data['userRank'] = Input::get('user_rank');
	        $data['userName'] = Input::get('user_name');
	        $data['departmentId'] = Input::get('department_id');
	        $data['departmentName'] = Input::get('department');
	        $data['deptDetail'] = Input::get('dept_detail');

	        $accountNameLabel = Lang::get('labels.login_account_name');
	        $passwordLabel = Lang::get('labels.login_password');
	        $userRankLabel = Lang::get('labels.user_rank');
	        $userNameLabel = Lang::get('labels.user_name');
	        $departmentLabel = Lang::get('labels.department');

	        $rankCodes = Code::withCategory('H001');

	        $ranks = array();

	        foreach ($rankCodes as $code) {
	            $ranks[] = $code->code;
	        }

	        $ranks = implode(',', $ranks);

	        $validator = Validator::make(array(
	                $accountNameLabel => $data['accountName'],
	                $passwordLabel => $data['password'],
	                $userRankLabel => $data['userRank'],
	                $userNameLabel => $data['userName'],
	                $departmentLabel => $data['departmentId']
	            ),
	            array(
	                $accountNameLabel => 'required|alpha_dash|between:4,30|unique:users,account_name',
	                $passwordLabel => "required|min:8|in:{$data['passwordConf']}",
	                $userRankLabel => "required|in:$ranks",
	                $userNameLabel => 'required|max:10',
	                $departmentLabel => 'required|exists:departments,id'
	            )
	        );

	        if ($validator->fails()) {
	            $data['messages'] = $validator->messages()->all();
	            $data['showCreateModal'] = true;
	        } else {
		        $user = Sentry::createUser(array(
			        'email'     => $data['accountName'],
			        'account_name' => $data['accountName'],
			        'password'  => $data['password'],
			        'activated' => true,
			        'user_rank' => $data['userRank'],
			        'dept_id'   => $data['departmentId'],
			        'dept_detail' => $data['deptDetail'],
			        'user_name' => $data['userName']
		    	));
	    	}
   		}

        //default form value
        $data = array_merge(array(
        		'showCreateModal'=>false,
                'accountName' => '',
                'userRank' => 'R011',
                'userName' => '',
                'departmentName' => '',
                'departmentId' => ''
            ), $data);
		return View::make('admin.users', $data);
	}

	public function getUsers()
	{
		$builder = DB::table('users')->leftJoin('codes', function($query){
			$query->on('codes.code','=','users.user_rank')
			->where('codes.category_code', '=', 'H001');
		})->leftJoin('departments', 'departments.id','=','users.dept_id')
		->select(array(
				'users.id',
				'users.account_name',
				'users.user_name',
				'codes.title',
				'departments.full_name',
				'users.dept_detail',
				'users.activated'
			));
		
		return Datatables::of($builder)->make();
	}

	public function showUserDetail($userId)
	{
        $codes = Code::in('H001');
        $ranks = array();
        foreach ($codes as $code) {
            $ranks[$code->code] = $code->title;
        }


		$user = User::where('id', '=', $userId)->with('rank', 'groups', 'department')->first();

		return View::make('admin.user-info', array(
			'user'=>$user,
			'ranks'=>$ranks,
			'groups'=>Group::all()
		));
	}

	public function showPermissions()
	{
		return View::make('main');
	}

	public function setUserActivated()
	{
		$activated = Input::get('activated');
		$ids = Input::get('ids');
		foreach ($ids as $id)
		{
			if (!is_numeric($id))
			{
				Log::error('requested user id is not numeric value');
				return Lang::get('strings.server_error');
			}
		}

		
		foreach ($ids as $id)
		{
			$user = User::find($id);
			if ($user->account_name === 'admin') 
			{
				return Lang::get('strings.cannot_deactivate_admin');
			}

			$user->activated = $activated;
			if ($activated) 
			{
				$user->activated_at = date('Y-m-d H:i:s');
			}
			$user->save();
		}
	}

	public function deleteUser()
	{
		$ids = Input::all();
		foreach ($ids as $id)
		{
			if (!is_numeric($id))
			{
				Log::error('requested user id is not numeric value');
				return Lang::get('strings.server_error');
			}
		}

		
		foreach ($ids as $id)
		{
			$user = User::find($id);
			if ($user->account_name === 'admin') 
			{
				return Lang::get('strings.cannot_delete_admin');
			}
			$user->delete();
		}
	}
	
	public function updateUser($userId)
	{
		$codes = Code::in('H001');
		$ranks = array();
		foreach ($codes as $c) $ranks[] = $c['code'];
		$ranks = implode(',',$ranks);

		$input = Input::all();

        $validator = Validator::make($input,
            array(
                'user_name' => 'required|max:10',
                'user_rank' => "required|in:$ranks",
                'department_id' => "required|exists:departments,id",
                'dept_detail' => 'max:100'
            )
        );

        if ($validator->fails())
        {
        	$msg = array();
        	foreach ($validator->messages()->all() as $m) $msg[] = $m;
        	$msg = implode('<br>', $msg);
        	Log::error('update user : input validation fails. '.$validator->messages()->all());

        	LayoutComposer::addNotification('error', $msg);
        	return $this->showUserDetail($userId);
        }

        $user = User::find($userId);

        $user->user_name = $input['user_name'];
        $user->user_rank = $input['user_rank'];
        $user->dept_id = $input['department_id'];
        $user->dept_detail = $input['dept_detail'];

        $user->groups()->detach();
        if (!isset($input['groups_ids']))
        {
        	$input['groups_ids'] = array();
        }
        foreach ($input['groups_ids'] as $groupId)
        {
        	$user->groups()->attach($groupId);
        }
        $user->push();
    	LayoutComposer::addNotification('success', Lang::get('strings.success_edit'));
        return $this->showUserDetail($userId);
	}
}
