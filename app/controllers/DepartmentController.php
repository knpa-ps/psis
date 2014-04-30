<?php

class DepartmentController extends BaseController {

	private $service;

	public function __construct() {
		$this->service = new DepartmentService;
	}

	public function getData() {
		$id = Input::get('id');
		$dept = Department::find($id);
		$details = array(
			'dept_name' => $dept->dept_name,
			'full_name' => $dept->full_name,
			'selectable' => $dept->is_selectable,
			'type_code' => $dept->type_code,
			'is_alive' => $dept->is_alive
			);
		return $details; 
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
		$id=Input::get('id');
		$dept=Department::find($id);
		$dept->is_alive = 0;
		$dept->save();
	}

	public function create() {
		$new = new Department;
		$new->parent_id = Input::get('parent_id');
		$new->depth = 0;
		$new->dept_name = Input::get('name');
		$new->full_path = '';
		$new->full_name = '';
		$new->is_alive = 1;
		$new->is_terminal = 0;
		$new->type_code = 'D003';
		$new->sort_order = Department::where('parent_id','=',$new->parent_id)->count();

		$new->save();

		$this->service->adjustHierarchy($new->id);
		
		return $new->id;
	}

	public function rename() {
		$id=Input::get('id');
		$dept = Department::find($id);
		$dept->dept_name = Input::get('name');
		$dept->save();
		$this->service->adjustHierarchy($id ? $id : null);
	}

	public function update() {
		$id = Input::get('id');
		$selectable = Input::get('selectable') == 1 ? 1 : 0 ;
		$childSelectable = Input::get('child_selectable') == 1 ? 1 : 0 ;
		$typeCode = Input::get('type_code');
		$childType = Input::get('child_type') == 1 ? 1 : 0 ;

		$this->service->detailUpdate($id, $selectable, $childSelectable, $typeCode, $childType);
	}
}
