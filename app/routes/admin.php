<?php 

/**
 * 시스템관리자(superuser 권한을 가진 계정)만 사용할 수 있는 routing group
 */
Route::group(array('before'=>'auth|permission:superuser'), function() {
    Route::group(array('prefix'=>'admin'), function() {
        Route::get('/', 'AdminController@displayDashboard');
        // 부서 관리
        Route::get('depts', 'AdminController@displayDeptTree');
        Route::post('depts/move', 'DepartmentController@move');
        Route::post('depts/delete', 'DepartmentController@delete');
        Route::post('depts/rename', 'DepartmentController@rename');
        Route::post('depts/update', 'DepartmentController@update');
        Route::post('depts/create', 'DepartmentController@create');
        Route::post('depts/data', 'DepartmentController@getData');

        Route::get('depts/adjust-positions', 'DepartmentController@adjustPositions');
        Route::get('depts/adjust-hierarchy', 'DepartmentController@adjustHierarchy');
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