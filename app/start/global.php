<?php

/*
|--------------------------------------------------------------------------
| Register The Laravel Class Loader
|--------------------------------------------------------------------------
|
| In addition to using Composer, you may use the Laravel class loader to
| load your controllers and models. This is useful for keeping all of
| your classes in the "global" namespace without Composer updating.
|
*/

ClassLoader::addDirectories(array(
));

/*
|--------------------------------------------------------------------------
| Application Error Logger
|--------------------------------------------------------------------------
|
| Here we will configure the error logger setup for the application which
| is built on top of the wonderful Monolog library. By default we will
| build a basic log file setup which creates a single file for logs.
|
*/

Log::useFiles(storage_path().'/logs/laravel.log');

/*
|--------------------------------------------------------------------------
| Application Error Handler
|--------------------------------------------------------------------------
|
| Here you may handle any errors that occur in your application, including
| logging them or displaying custom views for specific errors. You may
| even register several error handlers to handle different types of
| exceptions. If nothing is returned, the default error view is
| shown, which includes a detailed stack trace during debug.
|
*/

App::error(function(Exception $exception, $code)
{
	Log::error($exception);
});

/*
|--------------------------------------------------------------------------
| Maintenance Mode Handler
|--------------------------------------------------------------------------
|
| The "down" Artisan command gives you the ability to put an application
| into maintenance mode. Here, you will define what is displayed back
| to the user if maintenance mode is in effect for the application.
|
*/

App::down(function()
{
	return Response::make("Be right back!", 503);
});

/*
|--------------------------------------------------------------------------
| Require The Filters File
|--------------------------------------------------------------------------
|
| Next we will load the filters file for the application. This gives us
| a nice separate location to store our route and application filter
| definitions instead of putting them all in the main routes file.
|
*/

require app_path().'/filters.php';


/**
 * gnuboard 링크 주소
 * @param type $bo_table 
 * @param type $wr_id 
 * @return type
 */
if (!function_exists('board_url')) {
	function board_url($bo_table, $wr_id = null) {
		$params = array();
		$params['bo_table'] = $bo_table;
		if ($wr_id != null) {
			$params['wr_id'] = $wr_id;
		}

		$url = Config::get('app.gnuboard_base_url').'?'.http_build_query($params);

		return $url;
	}
}

/**
 * 문자열을 자른다. 만약 $len보다 짧으면 안 자름
 * @param string $str 원본 문자열
 * @param int $len 최대 길이
 * @param string $suffix 말줄임표
 * @return string 잘린 문자열
 */
if (!function_exists('cut_str')) {
	function cut_str($str, $len, $suffix = '...') {
		$charset = 'UTF-8';

		if(mb_strlen($str, $charset) > $len) {
		  $str = mb_substr($str, 0, $len, $charset) . $suffix;
		}

		return $str;
	}
}

HTML::macro('dataTables', function() {
	return HTML::style('static/vendor/datatables/1.10/css/jquery.dataTables.min.css').
	HTML::script('static/vendor/datatables/1.10/js/jquery.dataTables.min.js').
	HTML::script('static/js/jquery.dataTables.custom.js');
});


HTML::macro('datepicker', function() {
	return HTML::style('static/vendor/bootstrap-datepicker/css/datepicker3.css').
		HTML::script('static/vendor/bootstrap-datepicker/js/bootstrap-datepicker.js').
		HTML::script('static/vendor/bootstrap-datepicker/js/locales/bootstrap-datepicker.kr.js').
		HTML::script('static/vendor/bootstrap-datepicker/js/defaults.js');
});