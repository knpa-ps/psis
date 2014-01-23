<?php

class AdminController extends BaseController {

	public function showGroupList()
	{

		return View::make('admin.groups');
	}

	public function showUserList($data = array())
	{
        $codes = Code::in('H001');
        $codeSelectItems = array();
        foreach ($codes as $code) {
            $codeSelectItems[$code->code] = $code->title;
        }
        $data['codeSelectItems'] = $codeSelectItems;

        //default form value
        $data = array_merge(array(
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
		$builder = User::table()->select(array(
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

	public function showUserDetail($userId = null)
	{
        $codes = Code::in('H001');
        $ranks = array();
        foreach ($codes as $code) {
            $ranks[$code->code] = $code->title;
        }

        if ($userId)
        {
			$user = User::where('id', '=', $userId)->with('rank', 'groups', 'department')->first();
        }
        else
        {
        	$user = new User;
        }

		return View::make('admin.user-info', array(
			'user'=>$user,
			'ranks'=>$ranks,
			'groups'=>Group::all()
		));
	}

	public function showPermissions()
	{
		return View::make('admin.permissions');
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
                'dept_detail' => 'max:100',
                'password' => 'min:8|confirmed'
            )
        );

        if ($input['password'])
        {
        	$user = Sentry::findUserById($userId);
        	$user->password = $input['password'];
    		$user->save();
        }

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
    	LayoutComposer::addNotification('success', Lang::get('strings.success'));
        return $this->showUserDetail($userId);
	}

	public function insertUser()
	{
		$account = Input::get('account_name');
        $accountNameLabel = Lang::get('labels.login_account_name');
        $validator = Validator::make(array(
	        	$accountNameLabel => $account
		    ),
		    array(
		        $accountNameLabel => 'required|alpha_dash|between:4,30|unique:users,account_name'
		    )
		);

        if ($validator->fails())
        {
        	$msg = array();
        	foreach ($validator->messages()->all() as $m) $msg[] = $m;
        	$msg = implode('<br>', $msg);
        	Log::error('insert user : input validation fails. '.$validator->messages()->all());
        	LayoutComposer::addNotification('error', $msg);
        	return $this->showUserDetail();
        }

        $user = Sentry::createUser(array(
        	'account_name'=>$account,
        	'password'=>'tmppwd',
        	'email'=>$account,
        	'activated'=>true
        	));

        return $this->updateUser($user->id);

	}

	public function getGroups()
	{
		$builder = Group::select(array(
				'id',
				'name',
				'created_at'
			));
		
		return Datatables::of($builder)->make();
	}

	public function deleteGroup()
	{
		$ids = Input::all();
		foreach ($ids as $id)
		{
			if (!is_numeric($id))
			{
				Log::error('requested group id is not numeric value');
				return Lang::get('strings.server_error');
			}
		}
		
		foreach ($ids as $id)
		{
			$g = Sentry::findGroupById($id);
			if ($g->name === '관리자') 
			{
				return Lang::get('strings.cannot_delete_admin');
			}
			$g->delete();
		}
		return Lang::get('strings.success');
	}

	public function createGroup()
	{
		$gname = Input::get('groupName');
		Sentry::createGroup(array(
			'name'=>$gname
			));
		return Lang::get('strings.success');
	}
}
