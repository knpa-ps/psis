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


	// ManagerService class를 불러온다

	public function __construct() {
		$this->mngService = new ManagerService;
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
		$params = Input::all();
		$userId = $user->id;
		//초기 사용자그룹 선택되는거 만들어줘야함.
		//Input::get('group')이 처음엔 없으니까 $params['group']에 초기값을 넣어줘야함.
		$initSelectedGroup = Group::whereHas('users', function($q) use($userId) {
			$q->where('id','=',$userId);
		})->where('key','like','%.admin')->first();

		if($initSelectedGroup){
			if(!isset($params['group'])){
				$params['group'] = substr($initSelectedGroup->key, 0, strlen($initSelectedGroup->key)-6 );
			}
		}

		$users = $this->mngService->getUserListQuery($params, $user);

		$data['users'] = $users->paginate(15);
		$userGroups = $user->groups;

		foreach ($userGroups as $g) {
			if(substr($g->key, -5)==="admin"){
				$manageGroup = substr($g->key, 0, strlen($g->key)-6 );
				$data['manageGroups'][$manageGroup] = mb_substr($g->name, 0, mb_strlen($g->name)-8);
			}
		}

		return View::make('manager.users', $data);
	}

	/**
	 * Show the form for creating a new resource.
	 * GET /manager/users/create
	 *
	 * @return Response
	 */
	public function create()
	{
		$mngUser = Sentry::getUser();

		$codes = CodeCategory::ofName('H001')->first()->codes()->visible()->get();

        $userRanks = array();
        foreach ($codes as $code) {
            $userRanks[$code->code] = $code->title;
        }

        //default form value
        $form = array(
                'account_name' => '',
                'user_rank' => '',
                'user_name' => '',
                'dept_name' => '',
                'dept_id' => '',
                'contact'=>'',
                'contact_extension'=>'',
                'contact_phone'=>''
            );	

        $data['form'] = $form;
        $data['userRanks'] = $userRanks;
        $data['mngDeptId'] = $mngUser->dept_id;

		return View::make('manager.user-create',$data);
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /manager/users
	 *
	 * @return Response
	 */
	public function store()
	{
		$form = Input::all();
		try {

            $this->mngService->register($form);

        } catch (Exception $e) {
            Log::error($e->getMessage());

            switch ($e->getCode()) {
                case -1: // account_name 없음
                    Session::flash('message', Lang::get('auth.duplicate_account'));
                    break;
            }

            return $this->create();
        }

        // success!
        return Redirect::action('UserController@index')->with('message', '새 사용자 계정이 생성되었습니다.');
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
		$mngUser = Sentry::getUser();

		$codes = CodeCategory::ofName('H001')->first()->codes()->visible()->get();

        $userRanks = array();
        foreach ($codes as $code) {
            $userRanks[$code->code] = $code->title;
        }
        $user = User::find($id);

        $data['form'] = $user;
        $data['form']['userDept'] = $user->department;
        $data['userRanks'] = $userRanks;
        $data['status'] = $user->activated;
        $data['mngDeptId'] = $mngUser->dept_id;

		return View::make('manager.users-detail',$data);
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
		$user = User::find($id);

		if(!isset($user)){
			return App::abort(400);
		}

		$form = Input::all();
		//If password input is null, keep origin password
		if($form['password']){
			$user->password = $form['password'];
		}

		$user->user_name = $form['user_name'];
		$user->user_rank = $form['user_rank'];
		$user->dept_id = $form['dept_id'];

		$user->contact = $form['contact'];
		$user->contact_extension = $form['contact_extension'];
		$user->contact_phone = $form['contact_phone'];
		$user->activated = $form['status'];

		//변경한 정보 저장
		if(!$user->save()){
			return App::abort(500);
		}
		return Redirect::action('UserController@index')->with('message', '입력한 정보가 저장되었습니다.');

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