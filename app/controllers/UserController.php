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
		return View::make('user.password-mod');
	}

	public function displayDropout() {
		return View::make('user.dropout');
	}
}
