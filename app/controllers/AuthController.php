<?php

class AuthController extends BaseController {

	public function showLogin($message = '', $loginAttempts = 0)
	{
        $attemptsLimit = Config::get('cartalyst/sentry::throttling.attempt_limit');

        if ($loginAttempts > 0) {
           $message .= '\n\n'.Lang::get('strings.login_attempts_alert', array('limit'=>$attemptsLimit, 'attempts'=>$loginAttempts));
        }

        return View::make('auth/login', array('message'=>$message));
	}

    public function doLogin()
    {
        $accountName = Input::get('account_name');
        $password = Input::get('password');
        $remember = Input::get('remember') == TRUE;

        $accountNameLabel = Lang::get('labels.login_account_name');
        $passwordLabel = Lang::get('labels.login_password');

        $validator = Validator::make(array(
                $accountNameLabel => $accountName,
                $passwordLabel => $password
            ),
            array(
                $accountNameLabel => 'required|alpha_dash|between:4,30',
                $passwordLabel => 'required'
            )
        );

        if ($validator->fails()) {
            $messages = $validator->messages()->all();
            return $this->showLogin($messages[0], 0);
        }

        $attempts = 0;
        try
        {
            $throttle = Sentry::findThrottlerByUserLogin($accountName);
            $credentials = array(
                'account_name' => $accountName,
                'password'     => $password
            );

            $user = Sentry::authenticate($credentials, $remember);
            Sentry::login($user, false);
            return Redirect::to('/');
        }
        catch (Cartalyst\Sentry\Users\WrongPasswordException $e)
        {
            $message = Lang::get('strings.login_wrong_password');
            if (isset($throttle) && !$throttle->isSuspended()) {
                $throttle->addLoginAttempt();

                $attempts = $throttle->getLoginAttempts();
                $attemptLimit = Config::get('cartalyst/sentry::throttling.attempt_limit');

                if ($attemptLimit <= $attempts) {
                    $throttle->suspend();
                    $throttle->clearLoginAttempts();
                    $attempts = 0;
                    $message = Lang::get('strings.login_attempts_reached_limit');
                }
            }
        }
        catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
        {
            $message = Lang::get('strings.login_user_not_found');
        }
        catch (Cartalyst\Sentry\Users\UserNotActivatedException $e)
        {
            $message = Lang::get('strings.login_user_not_activated');
        }
        // The following is only required if throttle is enabled
        catch (Cartalyst\Sentry\Throttling\UserSuspendedException $e)
        {
            $message = Lang::get('strings.login_user_suspended');
        }
        catch (Cartalyst\Sentry\Throttling\UserBannedException $e)
        {
            $message = Lang::get('strings.login_user_banned');
        }
        return $this->showLogin($message, $attempts);
    }

    public function doLogout() {
        Sentry::logout();
        return Redirect::to('/');
    }

    public function showRegisterForm($data = array()) {
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

        Sentry::register(array(
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

        return $this->showLogin(Lang::get('strings.registered'));
    }

    public function showChangePassword() 
    {

    }

    public function doChangePassword() 
    {       
        
    }
}