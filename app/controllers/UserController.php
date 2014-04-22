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
}