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
/**
 * public pages
 */
Route::group(array('before'=>'guest'), function(){

    //로그인
    Route::get('/login', 'AuthController@showLogin');
    Route::post('/login', array(
        'before' => 'csrf',
        'uses' => 'AuthController@doLogin'
    ));

    //회원가입
    Route::post('/register', array(
        'before' => 'csrf',
        'uses' => 'AuthController@doRegister'
    ));
    Route::get('/register', 'AuthController@showRegisterForm');
 
});

//부서검색 관련
Route::group(array('prefix'=>'dept'), function(){
	
	Route::get('list', 'DepartmentController@showDeptTree');

	Route::group(array('before'=>'ajax'), function(){

		Route::get('children', 'DepartmentController@getChildren');

	});

});

/**
 *  pages that need login
 */ 
Route::group(array('before'=>'auth'), function() {
    Route::get('/', 'HomeController@showDashboard');
    Route::get('/logout', 'AuthController@doLogout');

    Route::get('/profile', 'HomeController@showProfile');

    Route::group(array('before'=>'admin', 'prefix'=>'admin'), function(){
        Route::get('/groups', 'AdminController@showGroupList');
        Route::get('/permissions', 'AdminController@showPermissions');
        Route::get('/users', 'AdminController@showUserList');
    });

    Route::get('/sl', 'BudgetController@showList');
    Route::get('/si', 'EscortEquipController@showInventory');
    Route::get('/sisi', 'ReportController@showList');
    Route::get('/sdfdd', 'ReportController@showComposeForm');
    
    Route::resource('users', 'UsersController');
});

