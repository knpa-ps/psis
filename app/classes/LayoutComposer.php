<?php 

class LayoutComposer {

	public function compose($view) 
	{
		$actionKey = Route::currentRouteAction();
		$action = Action::info($actionKey);

		$title = '';
		if ($action)
		{
			$title = $action->name;
		}

		try
		{
		    // Get the current active/logged in user
		    $user = Sentry::getUser();
		    $dept = Department::find($user->dept_id);
		    if (!$dept)
    		{
    			$dept = new Department;
    		}
		}
		catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
		{
			$dept = new Department;
			$user = new StdObj;
			Log::error('could not find current logged user information.');
		}
		$menus = Menu::tree(array());

		$breadcrumbs = Menu::breadcrumbs($menus);

		$view->with(array(
				'menus'=>$menus,
				'title'=>$title,
				'user'=>$user,
				'dept'=>$dept,
				'breadcrumbs'=>$breadcrumbs
			));
	}
}