<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
 */

// public
Route::group(array('before'=>'guest'), function(){

    //로그인
    Route::get('login', 'AuthController@displayLogin');
    Route::post('login', array(
        'before' => 'csrf',
        'uses' => 'AuthController@doLogin'
    ));

    //회원가입
    Route::post('register', array(
        'before' => 'csrf',
        'uses' => 'AuthController@doRegister'
    ));

    Route::get('register', 'AuthController@displayRegistrationForm');
});

Route::when('*', 'menu', array('GET'));

// private
Route::group(array('before'=>'auth'), function() {
    Route::get('/', array('uses'=>'HomeController@displayDashboard', 'before'=>'migrate'));

    Route::any('/migrate', array('uses'=>'HomeController@migrateV2'));

    Route::get('/reports', array('uses'=>'HomeController@displayDashboard'));    
    Route::get('/reports/list', array('uses'=>'HomeController@displayDashboard'));

    Route::get('logout', 'AuthController@doLogout');

});

/**
 *  조직도 조회 관련 ajax methods 
 */
Route::group(array('before'=>'ajax', 'prefix'=>'ajax'), function() {
    
    Route::post('dept_select_tree', function() {
        return View::make('widget.dept-selector-tree', Input::all());
    });

    Route::any('dept_tree', 'DepartmentController@getTreeNodes');

});

include('routes/users.php');
include('routes/reports.php');
include('routes/admin.php');
include('routes/budgets.php');
include('routes/eq.php');
include('routes/upload.php');
