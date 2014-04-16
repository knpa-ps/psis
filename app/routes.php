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
        Route::get('/adjustDepts', 'AdminController@adjustDepts');
        Route::get('/groups', 'AdminController@showGroupList');

        Route::get('/permissions', 'AdminController@showPermissions');
        Route::any('/users', 'AdminController@showUserList');

        Route::get('/menus', 'AdminController@showMenus');

        Route::get('/depts', 'AdminController@showDepts');
        
        Route::get('/user/new', 'AdminController@showUserDetail');
        Route::post('/user/new', 'AdminController@insertUser');
        Route::get('/user/{user_id}', 'AdminController@showUserDetail');
        Route::post('user/{user_id}', 'AdminController@updateUser');

        Route::group(array('before'=>'ajax'), function(){
            Route::post('/users/set_groups', 'AdminController@setUserGroups');
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
        Route::get('copy', 'ReportController@copyReport');
        Route::group(array('before'=>'ajax'), function(){
            Route::get('listData', 'ReportController@getReports');
            Route::post('setClosed', 'ReportController@setClosed');
            Route::post('edit', 'ReportController@editReport');

            Route::get('stat/user', 'ReportController@getUserStats');
            Route::get('stat/dept', 'ReportController@getDeptStats');
        });
    });

    Route::group(array('prefix'=>'budget'), function(){
        Route::group(array('prefix'=>'meal'), function(){
            Route::get('/payroll', 'BgMealPayController@show');
            Route::get('/stat', 'BgMealPayController@showSitStat');
            Route::get('/payroll/export', 'BgMealPayController@exportPayroll');
            Route::get('/stat/export', 'BgMealPayController@exportSitStat');            
            Route::group(array('before'=>'ajax'), function(){
                Route::post('/payroll/insert', 'BgMealPayController@insertPayroll');
                Route::post('/payroll/delete', 'BgMealPayController@deletePayroll');
                Route::get('/payroll/data', 'BgMealPayController@getPayrollData');

                Route::get('/stat/data', 'BgMealPayController@getSitStatData');
            });
        });

        Route::group(array('prefix'=>'mob'), function(){
            Route::get('/payroll', 'BgMobController@show');
            Route::get('/payroll/export', 'BgMobController@exportPayroll');

            Route::get('/stat/situation', 'BgMobController@showSitStat');
            Route::get('/stat/depts', 'BgMobController@showDeptStat');
            Route::get('stat/situation/export', 'BgMobController@exportSitStat');
            
            Route::group(array('before'=>'ajax'), function(){
                Route::get('payroll/data', 'BgMobController@getPayrollData');
                Route::post('payroll/insert', 'BgMobController@insertPayroll');
                Route::post('payroll/delete', 'BgMobController@deletePayroll');

                Route::get('stat/situation/data', 'BgMobController@getSitStatData');
            });
        });

        Route::group(array('prefix'=>'config'), function(){
            Route::any('/meal', 'BgConfigController@show');
            Route::any('/mob', 'BgConfigController@showMob');

            Route::group(array('before'=>'ajax'), function(){
                Route::get('/readCloseDates', 'BgConfigController@readCloseDates');
                Route::post('/createCloseDate', 'BgConfigController@createCloseDate');
                Route::post('/deleteCloseDates', 'BgConfigController@deleteCloseDates');
                Route::post('/updateMobCost', 'BgConfigController@updateMobCost');
            });
        });
    });

    Route::get('download', 'FileController@download');
});

Route::get('moveup', 'DepartmentController@moveUp');
Route::get('adjust', 'DepartmentController@adjust');
