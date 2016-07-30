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
        // 장비 관리
        Route::resource('/categories', 'EqCategoryController');
        Route::resource('/item_codes', 'EqItemCodeController');
        Route::get('/item_codes/{itemCode}/{itemId}', 'EqItemCodeController@showDetail');
        Route::resource('/item', 'EqItemController');

        // 노드 fullpath, fullname 정렬
        Route::get('node_adjust', 'AdminController@adjustHierarchy');
        // 경비, 방순대 산하 부서들 정렬
        Route::get('department_adjust', 'AdminController@departmentAdjust');

        // 인벤토리 타입 지운 데이터를 남은 타입 데이터에 더함
        Route::get('merge_inventory_count/{itemId}', 'AdminController@mergeInventoryCount');
        // 지워진 타입의 데이터를 날림
        Route::get('delete_type_data/{itemId}', 'AdminController@deleteTypeData');
        Route::get('insert_type_90', 'AdminController@insertType90');

        // 전부 캐시 날린것 다시 전원에 대해 생성해줌
        Route::get('make_cache_for_all', 'EquipController@makeCacheForAll');
        Route::get('make_cache_for_item/{itemId}', 'EquipController@makeCacheForItem');
        Route::get('make_cache_for_node/{nodeId}', 'EquipController@makeCacheForNode');
        // 캐시 있는지 체크
        Route::get('check_cache_for_all','EquipController@checkCacheForAll');

        Route::get('make_sub_cache/{itemId}', 'EquipController@makeSubCache');
        Route::get('make_sub_cache_for_code/{codeId}', 'EquipController@makeSubCacheForCode');
        Route::get('make_sub_cache_clear/{itemId}', 'EquipController@makeSubCacheClear');
        Route::get('make_sub_cache_for_all', 'EquipController@makeSubCacheForAll');
        Route::get('check_sub_cache_for_all','EquipController@checkSubCacheForAll');

        /* Custom Routes */
        // 특정 아이템 수량 다 날려버림
        Route::get('clear_item_data/{nodeId}/{itemId}','EquipController@clearItemData');
        Route::get('add_nodes','EquipController@addNodes');
        Route::get('add_node/{nodeId}','EquipController@addNode');
        Route::get('insert_node_and_manager','EquipController@insertNodeAndManager');
    });

    Route::resource('menu', 'MenuController');
});
