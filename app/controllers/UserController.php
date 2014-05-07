<?php

class UserController extends BaseController {

	public function displayProfile() {

		$user = Sentry::getuser();
		$groups = $user->groups;
		return View::make('user.profile',array('user'=>$user, 'groups'=>$groups ));
	}

	public function displayProfileEdit() {
		$codes = CodeCategory::ofName('H001')->first()->codes()->visible()->get();
		$user = Sentry::getuser();
        $userRanks = array();
        foreach ($codes as $code) {
            $userRanks[$code->code] = $code->title;
        }
        $data['userRanks'] = $userRanks;
        $data['user'] = $user;
		return View::make('user.profile-edit',$data);
	}

	public function contactMod() {
		$user = Sentry::getuser();
		$user->contact = Input::get('contact');
		$user->contact_extension = Input::get('contact_extension');
		$user->contact_phone = Input::get('contact_phone');
		$user->save();
	}

	public function generalMod() {
		$user = Sentry::getuser();

		$mod = new ModUser;
		$mod->user_id = $user->id;
		$mod->user_name = Input::get('user_name');
		$mod->user_rank = Input::get('user_rank');
		$mod->dept_id = Input::get('dept_id');
		$mod->save();
	}

	public function displayPasswordMod() {
		$user = Sentry::getuser();
		return View::make('user.password-mod',array('user'=>$user));
	}

	public function passwordMod() {

		try
		{
		    $user = Sentry::getuser();

		    if($user->checkPassword(Input::get('existing_pw')))
		    {
		        $user->password = Input::get('new_pw');
				$user->save();	
				Session::flash('message', '비밀번호가 변경되었습니다.');
				return Redirect::action('UserController@displayPasswordMod');
		    }
		    else
		    {
		        Session::flash('message', '비밀번호가 틀렸습니다.');
				return Redirect::action('UserController@displayPasswordMod');
		    }
		}
		catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
		{
		    echo 'User was not found.';
		}
	}


	/**
	 * Display a listing of the resource.
	 * GET /manager/users
	 *
	 * @return Response
	 */
	public function index()
	{
		$user = Sentry::getUser();

		$depts = Department::where('full_path','like',$user->department->first()->full_path.'%')->get();
		
		$users = array();
		foreach ($depts as $d) {
			if(count($d->members()->get())!==0 ){
				$members = $d->members()->get();
				foreach ($members as $m) {
					$users[] = $m;
				}
			}
		}
		var_dump((object) $users);

		$data['users'] = User::paginate(15);

		$userGroups = $user->groups;
		$userGroupsIds = array();
		

		foreach ($userGroups as $g) {
			$userGroupsIds[] = $g->id;
		}
		//경비속보 분임관리자임
		if(in_array(9,$userGroupsIds)){
			$data['manageGroups']['report'] = "경비속보 사용자";
		}

		//경비예산 분임관리자임
		if(in_array(11,$userGroupsIds)){
			$data['manageGroups']['budget'] = "경비예산 사용자";
		}

		return View::make('manager.users', $data);
	}

	/**
	 * Show the form for creating a new resource.
	 * GET /manager/users/create
	 *
	 * @return Response
	 */
	public function create($form=array())
	{
		$codes = CodeCategory::ofName('H001')->first()->codes()->visible()->get();

        $userRanks = array();
        foreach ($codes as $code) {
            $userRanks[$code->code] = $code->title;
        }

        //default form value
        $form = array_merge(array(
                'account_name' => '',
                'user_rank' => 'R011',
                'user_name' => '',
                'dept_name' => '',
                'dept_id' => '',
                'contact'=>'',
                'contact_extension'=>'',
                'contact_phone'=>''
            ), $form);

        $data['form'] = $form;
        $data['userRanks'] = $userRanks;
		
		return View::make('manager.users-detail',$data);
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /manager/users
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}

	/**
	 * Display the specified resource.
	 * GET /manager/users/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$data['user'] = User::find($id);

		return View::make('manager.users-detail', $data);
	}

	/**
	 * Show the form for editing the specified resource.
	 * GET /manager/users/{id}/edit
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$data['user'] = User::find($id);
		
		return View::make('manager.users-detail', $data);
	}

	/**
	 * Update the specified resource in storage.
	 * PUT /manager/users/{id}
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
	 * DELETE /manager/users/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

}