<?php

class AuthController extends BaseController {
    private static $test;

	public function showLogin() {
        $boardService = new BoardService;
        $writes = $boardService->getLastest('notice', 5, 15);

        $data = array(
                'writes' => $writes
            );

        return View::make('auth.login', $data);
	}

    public function doLogin() {
        $accountName = Input::get('account');
        $password = Input::get('password');
        $remember = Input::get('remember') == 1;

        $validator = Validator::make(Input::all(),
            array(
                'account' => 'required|alpha_dash|between:4,255',
                'password' => 'required|between:8,255'
            )
        );

        if ($validator->fails()) {
            $messages = $validator->messages()->all();
            Log::error('login input validation failed. ');
            Session::flash('message', Lang::get('error.invalid_input'));
            return Redirect::action('AuthController@showLogin');
        }

        $message = '';

        try {
            
            $credentials = array(
                'account_name' => $accountName,
                'password'     => $password
            );

            $user = Sentry::authenticate($credentials, $remember);

            Sentry::login($user, $remember);

            Log::info('logged in: '.$accountName);

            return Redirect::to('/');

        } catch (Cartalyst\Sentry\Users\WrongPasswordException $e) {

            Log::error('login failed. '.$e->getMessage());
            $message = Lang::get('error.wrong_password');

        } catch (Cartalyst\Sentry\Users\UserNotFoundException $e) {

            Log::error('login failed. '.$e->getMessage());
            $message = Lang::get('error.user_not_found');

        } catch (Cartalyst\Sentry\Users\UserNotActivatedException $e) {

            Log::error('login failed. '.$e->getMessage());
            $message = Lang::get('error.user_not_activated');

        }

        Session::flash('message', $message);

        return Redirect::action('AuthController@showLogin');
    }

    public function doLogout() {
        
        $accountName = Sentry::getUser()->account_name;
        Sentry::logout();

        Log::info('logged out: '.$accountName);

        return Redirect::action('AuthController@showLogin');
    }

    public function showRegistrationForm($data = array()) {
        $codes = Code::in('H001');
        $codeSelectItems = array();
        foreach ($codes as $code) {
            $codeSelectItems[$code->code] = $code->title;
        }

        //default form value
        $data = array_merge(array(
                'accountName' => '',
                'userRank' => 'R011',
                'userName' => '',
                'departmentName' => '',
                'departmentId' => '',
                'contact'=>'',
                'contact_extension'=>'',
                'contact_phone'=>''
            ), $data);


        $data['codeSelectItems'] = $codeSelectItems;
        return View::make('auth/register', $data);
    }

    public function doRegister() 
    {
        $data = array();
        $data['accountName'] = Input::get('account_name');
        $data['password'] = Input::get('password');
        $data['passwordConf'] = Input::get('password_confirmation');
        $data['userRank'] = Input::get('user_rank');
        $data['userName'] = Input::get('user_name');
        $data['departmentId'] = Input::get('department_id');
        $data['deptDetail'] = Input::get('dept_detail');
        $data['contact'] = Input::get('contact');
        $data['contact_extension'] = Input::get('contact_extension');
        $data['contact_phone'] = Input::get('contact_phone');

        $accountNameLabel = Lang::get('labels.login_account_name');
        $passwordLabel = Lang::get('labels.login_password');
        $userRankLabel = Lang::get('labels.user_rank');
        $userNameLabel = Lang::get('labels.user_name');
        $departmentLabel = Lang::get('labels.department');
        $contactLabel = '일반전화';
        $contactExtLabel = '경비전화';
        $contactPhoneLabel = '핸드폰';

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
            return $this->showRegisterForm($data);
        }

        $deptId = $data['departmentId'];
        if (Department::find($deptId)->depth == 1) {
            $data['messages'] = array('부서를 하위 소속까지 정확히 선택해주세요. 지방청관리자 계정 생성은 8-1170으로 문의주시기 바랍니다.');
            return $this->showRegisterForm($data);
        }

        $user = Sentry::register(array(
            'email' => $data['accountName'],
            'account_name' => $data['accountName'],
            'password' => $data['password'],
            'user_name' => $data['userName'],
            'user_rank' => $data['userRank'],
            'dept_detail' => $data['deptDetail'],
            'dept_id' => $data['departmentId'],
            'contact'=>$data['contact'],
            'contact_extension'=>$data['contact_extension'],
            'contact_phone'=>$data['contact_phone']
        ));
        $user->groups()->detach();
        //default groups
        $defaults = Group::defaults()->get();

        foreach ($defaults as $d) {
            $user->groups()->attach($d->id);
        }

        $user->push();
        return $this->showLogin(Lang::get('strings.registered'));
    }

    public function showChangePassword() 
    {

    }

    public function doChangePassword() 
    {       
        
    }
}