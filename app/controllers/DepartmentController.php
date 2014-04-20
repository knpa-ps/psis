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

	public function moveUp() {
		$deptId = Input::get('value');

		$dept = Department::where('id', '=', $deptId)->first();

		if (!$dept) {
			return App::abort(400);
		}

		$toParentId = $dept->parent_id;

		$children = DB::table('departments')->select(array('id'))->where('full_path', 'like', '%:'.$dept->id.':%')->get();
		$fromIds = array($deptId);
		foreach ($children as $c) $fromIds[] = $c->id;

		DB::table('departments')
			->whereIn('id', $fromIds)
			->update(array('is_alive'=>0));

		$data = array();
		foreach ($fromIds as $f) {
			$d['dept_id_from'] = $f;
			$d['dept_id_to'] = $toParentId;
			$data[] = $d;
		}

		$is_terminal = DB::table('departments')->where('parent_id','=',$toParentId)->where('is_alive','=',1)->count()==0;
		DB::table('departments')->where('id','=',$toParentId)->update(array('is_terminal'=>$is_terminal));

		DB::table('dept_adjust')->insert($data);
	}

	public function adjust() {
		
	}
}
