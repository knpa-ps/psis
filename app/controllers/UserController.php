<?php

class UserController extends BaseController {

	public function showProfile() {

		$user = Sentry::getuser();
		$groups = $user->groups;
		return View::make('user.profile',array('user'=>$user, 'groups'=>$groups ));
	}

	public function showProfileEdit() {
		$codes = CodeCategory::ofName('H001')->first()->codes()->visible()->get();

        $userRanks = array();
        foreach ($codes as $code) {
            $userRanks[$code->code] = $code->title;
        }
        $data['userRanks'] = $userRanks;
		return View::make('user.profile_edit',$data);
	}

	public function contactMod() {
		$user = Sentry::getuser();
		$user->contact = $contact;
		$user->contact_extension = $contact_extension; 
		$user->contact_phone = $contact_phone;
		$user->save();
	}
}
