<?php 

class DepartmentService extends BaseService {

	/**
	 * 부서의 depth, full path, terminal 등 부서 계층에 관련된 정보들을 조정한다.
	 * @param int 이 부서의 하위부서들에 대한 계층 정보를 조정한다.
	 */
	public function adjustHierarchy($parentId = null) {
		
		if ($parentId === null) {
			$dept = null;
		} else {
			$dept = Department::find($parentId);

			if ($dept === null) {
				throw new Exception('department does not exists with id='.$deptId);
			}
		}

		DB::beginTransaction();

		$this->doAdjustHierarchy($dept);

		DB::commit();
	}

	private function doAdjustHierarchy(Department $parent = null) {

		if ($parent === null) {
			$parent = Department::whereNull('parent_id')->first();
			if ($parent === null) {
				throw new Exception('root department does not exist.');
			}
		}

		$children = $parent->children()->with('children')->get();

		// break point : 하위 부서가 없으면 break
		foreach ($children as $child) {

			// 하위부서의 계층 정보를 업데이트
			$child->full_path = rtrim($parent->full_path, ':').":{$child->id}:";
			$child->full_name = trim($parent->full_name." {$child->dept_name}");
			$child->depth = $parent->depth+1;

			$child->is_terminal = $child->children()->where('is_alive', '=', 1)->count() == 0;

			if (!$child->save()) {
				throw new Exception('failed to update department data. '.$child);
			}

			// traverse
			$this->doAdjustHierarchy($child);
		}
	}

	/**
	 * $deptId에 해당하는 부서를 주 부서로 설정한다.
	 * @param int $deptId 
	 */
	public function setHead($deptId) {
		$dept = Department::find($deptId);

		if ($dept === null) {
			throw new Exception('department does not exists with id='.$deptId);
		}

		Department::where('parent_id', '=', $dept->parent_id)
					->where('id', '!=', $dept->id)
					->update(array('is_head' => 0));

		$dept->is_head = 1;

		if (!$dept->save()) {
			throw new Exception('failed to update department data. '.$dept);
		}
	}
}