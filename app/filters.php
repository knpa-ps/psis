<?php

/*
|--------------------------------------------------------------------------
| Application & Route Filters
|--------------------------------------------------------------------------
|
| Below you will find the "before" and "after" events for the application
| which may be used to do any work before or after a request into your
| application. Here you may also register your custom route filters.
|
*/

App::before(function($request)
{
});


App::after(function($request, $response)
{
});

App::error(function($e, $code){
	if (Request::ajax()) {
		return Response::make($e->getMessage(), $code); 
	} else {
		switch ($code) {
			case 404:
			case 403:
				$header = '권한 없음';
				$message = '해당 작업에 대한 권한이 없습니다. 관리자에게 문의해주세요.';
				return View::make('errors.error', array('header'=>$header,'message'=>$message));
		}
	}
});

/*
|--------------------------------------------------------------------------
| Authentication Filters
|--------------------------------------------------------------------------
|
| The following filters are used to verify that the user of the current
| session is logged into this application. The "basic" filter easily
| integrates HTTP Basic authentication for quick, simple checking.
|
*/

Route::filter('auth', function()
{
	if (!Sentry::check()) return Redirect::guest('login');
});

Route::filter('ajax', function()
{
	if (!Request::ajax()) return App::abort(403, 'Unauthorized Action');
});

Route::filter('admin', function()
{
	try 
	{
		if (!Sentry::getUser()->hasAccess('admin'))
		{
			return App::abort(403, 'Unauthorized Action');
		}
	}
	catch (Cartalyst\Sentry\UserNotFoundException $e)
	{
	    return Redirect::to('/');
	}
});

/*
|--------------------------------------------------------------------------
| Guest Filter
|--------------------------------------------------------------------------
|
| The "guest" filter is the counterpart of the authentication filters as
| it simply checks that the current user is not logged in. A redirect
| response will be issued if they are, which you may freely change.
|
*/

Route::filter('guest', function()
{
	if (Sentry::check()) return Redirect::to('/');
});

/*
|--------------------------------------------------------------------------
| CSRF Protection Filter
|--------------------------------------------------------------------------
|
| The CSRF filter is responsible for protecting your application against
| cross-site request forgery attacks. If this special token in a user
| session does not match the one given in this request, we'll bail.
|
*/

Route::filter('csrf', function()
{
	if (Session::token() != Input::get('_token'))
	{
		throw new Illuminate\Session\TokenMismatchException;
	}
});

/**
 * View Composers
 */

View::composer('parts.notification', 'NotificationComposer');

Route::filter('menu', function(){

	$action = Route::currentRouteAction();
	$routes = explode('@', $action);
	$method = $routes[1];
	if (substr($method, 0, 4) == 'show')
	{
		$menuService = new MenuService;
		$menuService->setActiveMenu($action);
	}
});
