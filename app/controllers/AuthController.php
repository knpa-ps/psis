<?php

class AuthController extends BaseController {
    private static $test;

	public function displayLogin() {
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
            return Redirect::action('AuthController@displayLogin');
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

        return Redirect::action('AuthController@displayLogin');
    }

    public function doLogout() {

        $accountName = Sentry::getUser()->account_name;
        Sentry::logout();

        Log::info('logged out: '.$accountName);
        Session::flash('message', Lang::get('auth.logged_out'));

        return Redirect::action('AuthController@displayLogin');
    }

    public function displayRegistrationForm($form = array()) {
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
        return View::make('auth.register', $data);
    }

    public function doRegister()
    {
        $form = Input::all();

        $validator = Validator::make($form,
            array(
                'account_name' => 'required|alpha_dash|between:4,255',
                'password' => "required|between:8,255|confirmed",
                'user_rank' => "required",
                'user_name' => 'required|max:10',
                'dept_id' => 'required|exists:departments,id'
            )
        );

        // input validation
        if ($validator->fails()) {
            Log::error('registration input validation failed. ');
            Session::flash('message', Lang::get('error.invalid_input'));
            return $this->displayRegistrationForm($form);
        }

        // do register
        $service = new UserService;
        try {

            $service->register($form);

        } catch (Exception $e) {
            Log::error($e->getMessage());

            switch ($e->getCode()) {
                case -1: // account_name 중복
                    Session::flash('message', Lang::get('auth.duplicate_account'));
                    break;
            }

            return $this->displayRegistrationForm($form);
        }

        // success!
        Session::flash('message', Lang::get('auth.registration_success'));
        return Redirect::action('AuthController@displayLogin');
    }

    public function displayChangePassword()
    {

    }

    public function doChangePassword()
    {

    }
}
