[
	{
		"id" : 1,
		"module_id" : 2,
		"type" : 1,
		"action" : "ReportController@showList",
		"name" : "경비상황관리",
		"menu_id": 2,
		"menu_default": 1
	},
	{
		"id" : 2,
		"module_id" : 2,
		"type" : 1,
		"action" : "ReportController@showComposeForm",
		"name" : "경비상황작성",
		"menu_id": 3,
		"menu_default": 1
	},

	{
		"id" : 100,
		"module_id" : 3,
		"type" : 1,
		"action" : "BudgetController@showList",
		"name" : "경비예산조회",
		"menu_id": 201,
		"menu_default": 1
	},
	{
		"id" : 200,
		"module_id" : 4,
		"type" : 1,
		"action" : "EscortEquipController@showInventory",
		"name" : "경호장비 보유현황",
		"menu_id": 300,
		"menu_default": 1
	},

	{
		"id" : 300,
		"module_id" : 5,
		"type" : 1,
		"action" : "AdminController@showUserList",
		"name" : "사용자 관리",
		"menu_id": 501,
		"menu_default": 1
	},
	{
		"id" : 301,
		"module_id" : 5,
		"type" : 1,
		"action" : "AdminController@showGroupList",
		"name" : "그룹 관리",
		"menu_id": 502,
		"menu_default": 1
	},
	{
		"id" : 302,
		"module_id" : 5,
		"type" : 1,
		"action" : "AdminController@showPermissions",
		"name" : "권한 관리",
		"menu_id": 503,
		"menu_default": 1
	},

	{
		"id" : 303,
		"module_id" : 5,
		"type" : 1,
		"action" : "AdminController@showUserDetail",
		"name" : "사용자 정보 조회",
		"menu_id": 501,
		"menu_default": 0
	},
	{
		"id" : 304,
		"module_id" : 5,
		"type" : 1,
		"action" : "AdminController@updateUser",
		"name" : "사용자 정보 업데이트",
		"menu_id": 501,
		"menu_default": 0
	},
	{
		"id" : 305,
		"module_id" : 5,
		"type" : 1,
		"action" : "AdminController@deleteUser",
		"name" : "사용자 정보 삭제",
		"menu_id": 501,
		"menu_default": 0
	},
	{
		"id" : 306,
		"module_id" : 5,
		"type" : 1,
		"action" : "AdminController@setUserActivated",
		"name" : "사용자 상태변경",
		"menu_id": 501,
		"menu_default": 0
	}

]