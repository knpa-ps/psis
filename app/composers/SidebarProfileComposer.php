<?php 

class SidebarProfileComposer {

	public function compose($view) {
		$user = Sentry::getUser();
		$dept = $user->department;
		$groups = $user->groups()->get();

		$view->with(array(
			'user' => $user,
			'dept' => $dept,
			'groups' => $groups
		));
	}
	
}

