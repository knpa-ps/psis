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
    Route::get('/', array('uses'=>'HomeController@displayDashboard'));

    Route::get('/report', array('uses'=>'HomeController@displayDashboard'));    
    Route::get('/report/list', array('uses'=>'HomeController@displayDashboard'));

    Route::get('logout', 'AuthController@doLogout');

    /**
     * 시스템관리자(superuser 권한을 가진 계정)만 사용할 수 있는 routing group
     */
    Route::group(array('before'=>'permission:superuser'), function() {
        Route::group(array('prefix'=>'admin'), function() {
            Route::get('/', 'AdminController@displayDashboard');
            // 부서 관리
            Route::get('dept', 'AdminController@displayDeptTree');
            // 메뉴 관리
            Route::get('menu', array('uses'=>'AdminController@displayMenuTree'));

            // 그룹 관리
            Route::get('groups', 'AdminController@displayUserGroups');

            Route::get('groups/data', 'AdminController@getUserGroupsData');
            Route::get('groups/create_modal', 'AdminController@displayGroupCreateModal');
            Route::get('groups/modify_modal/{group_id}', 'AdminController@displayGroupModifyModal');
            Route::post('groups/create', 'AdminController@createUserGroup');
            Route::post('groups/modify', 'AdminController@modifyUserGroup');
            Route::post('groups/delete', 'AdminController@deleteUserGroup');
            Route::post('groups/edit', 'AdminController@editUserGroup');

            Route::post('groups/users', 'AdminController@displayUsersSelectModal');
            Route::get('groups/users/all', 'AdminController@getUserAll');
            Route::get('groups/users/data', 'AdminController@getUserGroupUsersData');
            Route::post('groups/users/add', 'AdminController@addUsersToUserGroup');
            Route::post('groups/users/remove', 'AdminController@removeUsersFromUserGroup');
            // 권한 관리
            Route::get('permission', 'AdminController@displayPermissionMng');
            Route::post('permission/get', 'AdminController@getPermission');
            Route::post('permission/save', 'AdminController@savePermission');

        });

        Route::resource('menu', 'MenuController');
    });
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
