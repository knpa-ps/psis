<?php

class UserController extends BaseController {

	public function showProfile() {

		return View::make('user.profile');
	}

	public function isUniqueAccountName()
	{
		$account = Input::get('value');
        $accountNameLabel = Lang::get('labels.login_account_name');
        $validator = Validator::make(array(
	        	$accountNameLabel => $account
		    ),
		    array(
		        $accountNameLabel => 'unique:users,account_name'
		    )
		);

        $result = array("value"=>$account);

		if ($validator->fails())
		{
			$result['valid'] = 0;
			$result['message'] = $validator->messages()->first($accountNameLabel);
		}
		else
		{
			$result['valid'] = 1;
			$result['message'] = '';
		}

		return json_encode($result);
	}

	public function changePassword()
	{
		$oldPw = Input::get('old_password');
		$newPw = Input::get('password');

		$validator = Validator::make(Input::all(),
			array(
				'old_password'=>'required',
				'password'=>'required|min:8|confirmed'
			));

		if ($validator->fails()) {
			return -1;
		} 

		if (!Sentry::checkPassword($oldPw)) {
			return -2;
		}

		$user = Sentry::getUser();
		$user->password = $newPw;
		$user->save();
		return 0;
	}
}
