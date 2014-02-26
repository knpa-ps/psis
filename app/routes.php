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

/**
 * global patterns
 */
Route::pattern('user_id', '[0-9]+');

/**
 * public pages
 */
Route::group(array('before'=>'ajax'), function(){
    Route::get('isUniqueAccountName', 'UserController@isUniqueAccountName');
});

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
Route::group(array('before'=>'auth|menu'), function() {
    Route::get('/', 'HomeController@showDashboard');
    Route::post('/setConfigs', 'HomeController@setConfigs');
    Route::get('/logout', 'AuthController@doLogout');


    Route::group(array('before'=>'admin', 'prefix'=>'admin'), function(){
        Route::any('/', 'AdminController@showUserList');

        Route::get('/groups', 'AdminController@showGroupList');

        Route::get('/permissions', 'AdminController@showPermissions');
        Route::any('/users', 'AdminController@showUserList');

        Route::get('/menus', 'AdminController@showMenus');
        
        Route::get('/user/new', 'AdminController@showUserDetail');
        Route::post('/user/new', 'AdminController@insertUser');
        Route::get('/user/{user_id}', 'AdminController@showUserDetail');
        Route::post('user/{user_id}', 'AdminController@updateUser');

        Route::group(array('before'=>'ajax'), function(){
            Route::get('/users/get', 'AdminController@getUsers');
            Route::get('groups/get', 'AdminController@getGroups');
            Route::post('groups/delete', 'AdminController@deleteGroup');
            Route::post('groups/create', 'AdminController@createGroup');

            Route::post('user/delete', 'AdminController@deleteUser');
            Route::post('user/actiavted', 'AdminController@setUserActivated');

            Route::post('permissions/update', 'AdminController@updatePermissions');
        });
    });

    Route::group(array('prefix'=>'user'), function(){

        Route::group(array('before'=>'ajax'), function(){
            Route::post('changePassword', 'UserController@changePassword');
        });
    });

    Route::group(array('prefix'=>'reports'), function(){
        Route::get('/list', 'ReportController@showList');
        Route::get('/my', 'ReportController@showMyReports');
        Route::get('/stats', 'ReportController@showStats');
        Route::get('/compose', 'ReportController@showComposeForm');
        Route::post('uploadAttachment', 'ReportController@uploadAttachments');
        Route::post('create', 'ReportController@insertReport');
        Route::get('detail', 'ReportController@showReport');
        Route::group(array('before'=>'ajax'), function(){
            Route::get('listData', 'ReportController@getReports');
            Route::post('setClosed', 'ReportController@setClosed');
            Route::post('edit', 'ReportController@editReport');
        });
    });

    Route::group(array('prefix'=>'budget'), function(){
        Route::group(array('prefix'=>'meal'), function(){
            Route::get('/', 'BgMealPayController@show');
            Route::get('/export', 'BgMealPayController@export');
            
            Route::group(array('before'=>'ajax'), function(){
                Route::post('/create', 'BgMealPayController@create');
                Route::post('/update', 'BgMealPayController@update');
                Route::post('/delete', 'BgMealPayController@delete');
                Route::get('/read', 'BgMealPayController@read');
                Route::post('/setClosed', 'BgMealPayController@setClosed');
            });
        });

        Route::group(array('prefix'=>'mob'), function(){
            Route::get('/', 'BgMobController@show');
        });

        Route::group(array('prefix'=>'config'), function(){
            Route::any('/', 'BgConfigController@show');

            Route::group(array('before'=>'ajax'), function(){
                Route::get('/readCloseDates', 'BgConfigController@readCloseDates');
                Route::post('/createCloseDate', 'BgConfigController@createCloseDate');
                Route::post('/deleteCloseDates', 'BgConfigController@deleteCloseDates');
            });
        });
    });

    Route::get('download', 'FileController@download');
});

