[
	{
		"id": 1,
		"parent_id": 0,
		"name": "경비상황",
		"action": "",
		"is_shortcut" : 0,
		"group_ids" : "",
		"sort_order" : 1
	},
	{
		"id": 2,
		"parent_id": 1,
		"name": "경비상황관리",
		"action": "ReportController@showList",
		"is_shortcut" : 0,
		"group_ids" : "",
		"sort_order" : 2
	},
	{
		"id": 3,
		"parent_id": 1,
		"name": "경비상황작성",
		"action": "ReportController@showComposeForm",
		"is_shortcut" : 0,
		"group_ids" : "",
		"sort_order" : 3
	},

	{
		"id": 200,
		"parent_id": 0,
		"name": "경비예산",
		"action": "",
		"is_shortcut" : 0,
		"group_ids" : "",
		"sort_order" : 4
	},
	{
		"id": 201,
		"parent_id": 200,
		"name": "동원급식비",
		"action": "BgMealPayController@show",
		"is_shortcut" : 0,
		"group_ids" : "",
		"sort_order" : 5
	},
	{
		"id": 202,
		"parent_id": 200,
		"name": "경비동원수당",
		"action": "BgMobPayController@show",
		"is_shortcut" : 0,
		"group_ids" : "",
		"sort_order" : 6
	},

	{
		"id": 203,
		"parent_id": 200,
		"name": "예산환경설정",
		"action": "BgConfigController@show",
		"is_shortcut" : 0,
		"group_ids" : "",
		"sort_order" : 7
	},

	{
		"id": 300,
		"parent_id": 0,
		"name": "경호장비",
		"action": "HomeController@showDashboard",
		"is_shortcut" : 0,
		"group_ids" : "",
		"sort_order" : 6
	},

	{
		"id": 301,
		"parent_id": 300,
		"name": "경호장비관리",
		"action": "HomeController@showDashboard",
		"is_shortcut" : 0,
		"group_ids" : "",
		"sort_order" : 7
	},

	{
		"id": 302,
		"parent_id": 300,
		"name": "경호장비보유내역",
		"action": "HomeController@showDashboard",
		"is_shortcut" : 0,
		"group_ids" : "",
		"sort_order" : 8
	},

	{
		"id": 303,
		"parent_id": 300,
		"name": "경호장비 급/대여대장",
		"action": "HomeController@showDashboard",
		"is_shortcut" : 0,
		"group_ids" : "",
		"sort_order" : 9
	},

	{
		"id": 500,
		"parent_id": 0,
		"name": "시스템관리",
		"action": "AdminController@showUserList",
		"is_shortcut" : 0,
		"group_ids" : "1",
		"sort_order" : 7
	},
	{
		"id": 501,
		"parent_id": 500,
		"name": "사용자 관리",
		"action": "AdminController@showUserList",
		"is_shortcut" : 0,
		"group_ids" : "1",
		"sort_order" : 8
	},
	{
		"id": 502,
		"parent_id": 500,
		"name": "그룹 관리",
		"action": "AdminController@showGroupList",
		"is_shortcut" : 0,
		"group_ids" : "1",
		"sort_order" : 9
	},
	{
		"id": 503,
		"parent_id": 500,
		"name": "권한 관리",
		"action": "AdminController@showPermissions",
		"is_shortcut" : 0,
		"group_ids" : "1",
		"sort_order" : 10
	}
]