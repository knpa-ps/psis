<?php

class DepartmentController extends BaseController {

	public function showDeptTree() {
		$title = Lang::get('strings.title_dept_list');
		return View::make('dept/dept_list', array('title'=>$title));
	}

	public function getChildren() {
		$root = Input::get('root');

		if ($root == 'source') {
			$depts = Department::children(0);
		} else {
			$depts = Department::children($root);
		}

		$result = array();

		foreach ($depts as $dept) 
		{
			$result[] = array(
					'text'=>$dept->dept_name,
					'hasChildren'=>$dept->is_terminal == 0,
					'id'=>$dept->id,
					'data'=>$dept->parseFullName()
				);
		}

		return json_encode($result);
	}
}
