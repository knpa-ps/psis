<?php

class DepartmentController extends BaseController {

	private $service;

	public function __construct() {
		$this->service = new DepartmentService;
	}

	public function getTreeNodes() {
		$parentId = Input::get('id');

		$depts = $this->service->getAliveChildren($parentId === '#' ? null : $parentId);

		$nodes = array();

		foreach ($depts as $dept) {
			$nodes[] = array(
					'id' => $dept->id,
					'text' => $dept->dept_name,
					'children' => $dept->is_terminal?array():true,
					'li_attr' => array( 
						'data-is-alive' => $dept->is_alive,
						'data-type-code' => $dept->type_code,
						'data-full-name' => $dept->full_name,
						'data-selectable' => $dept->is_selectable
						)
				);
		}

		return $nodes;
	}

	public function showDeptTree() {
		$title = Lang::get('strings.title_dept_list');
		return View::make('dept/dept_list', array('title'=>$title));
	}

	public function adjustPositions() {
		set_time_limit(0);
		$parentId = Input::get('id');
		if ($parentId) {
			$children = Department::find($parentId)->children()->orderBy('sort_order', 'asc')->get();
		} else {
			$children = Department::with('children')->regions()->orderBy('sort_order', 'asc')->get();
		}
		$this->service->adjustPositions($children);
		return '완료되었습니다';
	}

	public function adjustHierarchy() {
		set_time_limit(0);
		$parentId = Input::get('id');

		$this->service->adjustHierarchy($parentId ? $parentId : null);
		return '완료되었습니다';
	}

	public function move() {
		$id = Input::get('id');
		$parentId = Input::get('parent_id');
		$position = Input::get('position');
		try {
			$this->service->move($id, $parentId, $position);
		} catch (Exception $e) {
			return App::abort(500);
		}
	}

	public function delete() {

	}

	public function create() {

	}

	public function update() {

	}
}
