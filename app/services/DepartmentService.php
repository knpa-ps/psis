<?php 

class DepartmentService extends BaseService {
	public function detailUpdate($id, $selectable, $childSelectable, $typeCode, $childType){
		$dept = Department::find($id);
		if ($dept === null) {
			throw new Exception('department does not exists with id='.$id);
		}

		$dept->is_selectable = $selectable;
		$dept->type_code = $typeCode;
		if(!$dept->save()){
			throw new Exception('failed to update department data. '.$dept);
		}


		if($childSelectable==1) {
			if($selectable==1) {
				Department::where('full_path', 'like', $dept->full_path.'%' )->update(array(
					'is_selectable' => 1
				));	
			}
			else {
				Department::where('full_path', 'like', $dept->full_path.'%' )->update(array(
					'is_selectable' => 0
				));		
			}
		}
		
		if($childType==1){
			Department::where('full_path', 'like', $dept->full_path.'%')->update(array(
				'type_code' => $typeCode
			));
		}
	}

	public function getAliveChildren($parentId = null) {
		
		if ($parentId === null) {

			$depts = Department::regions()->alive();

		} else {

			$parent = Department::find($parentId);

			if ($parent === null) {
				throw new Exception('department does not exists with id='.$parentId);
			}

			$depts = $parent->children()->alive();
		}

		return $depts->orderBy('sort_order', 'asc')->get();
	}

	public function adjustPositions($children = null) {
		DB::beginTransaction();
		$this->doAdjustPositions($children);
		DB::commit();
	}

	private function doAdjustPositions($siblings) {
		$position = 0;
		foreach ($siblings as $dept) {

			$dept->sort_order = $position;
			$dept->save();

			$this->doAdjustPositions($dept->children()->orderBy('sort_order', 'asc')->get());

			$position++;
		}
	}

	/**
	 * 부서의 depth, full path, terminal 등 부서 계층에 관련된 정보들을 조정한다.
	 * @param int 이 부서와 하위부서들에 대한 계층 정보를 조정한다.
	 */
	public function adjustHierarchy($deptId = null) {
		DB::beginTransaction();

		if ($deptId === null) {
			$dept = null;
		} else {
			$dept = Department::with('children')->find($deptId);

			if ($dept === null) {
				throw new Exception('department does not exists with id='.$deptId);
			}

			$parent = $dept->parent()->first();

			if ($parent === null) {
				$dept->full_path = ":{$dept->id}:";
				$dept->full_name = $dept->dept_name;
				$dept->depth = 1;	
			} else {
				$dept->full_path = rtrim($parent->full_path, ':').":{$dept->id}:";
				$dept->full_name = trim($parent->full_name." {$dept->dept_name}");
				$dept->depth = $parent->depth+1;					
			}

			$dept->is_terminal = $dept->children()->alive()->count() == 0;

			if (!$dept->save()) {
				throw new Exception('failed to update department data. '.$child);
			}

		}

		$this->doAdjustHierarchy($dept);

		DB::commit();
	}

	private function doAdjustHierarchy(Department $parent = null) {

		if ($parent === null) {
			$children = Department::regions()->orderBy('sort_order', 'asc')->get();
			$parent = new stdClass;
			$parent->full_path = '';
			$parent->full_name = '';
			$parent->depth = 0;
		} else {
			$children = $parent->children()->orderBy('sort_order', 'asc')->with('children')->get();
		}

		// break point : 하위 부서가 없으면 break
		foreach ($children as $child) {

			// 하위부서의 계층 정보를 업데이트
			$child->full_path = rtrim($parent->full_path, ':').":{$child->id}:";
			$child->full_name = trim($parent->full_name." {$child->dept_name}");
			$child->depth = $parent->depth+1;
			$child->is_terminal = $child->children()->alive()->count() == 0;

			if (!$child->save()) {
				throw new Exception('failed to update department data. '.$child);
			}

			// traverse
			$this->doAdjustHierarchy($child);
		}
	}

	public function move($id, $parentId, $position) {

		$Department = Department::find($id);

		if ($Department === null) {
			throw new Exception('Department not found with id='.$id);
		}

		// 같은 레벨에 있는 메뉴들 중 sort_order가 옮겨진 메뉴보다 나중에 있는 메뉴들에 대해 sort_order - 1
		$oldSiblings = Department::where('parent_id', '=', $Department->parent_id)
						->where('sort_order', '>', $Department->sort_order)
						->get();

		DB::beginTransaction();

		foreach ($oldSiblings as $s) {
			$s->sort_order -= 1;
			if (!$s->save()) {
				throw new Exception('db failed during updating Department='.$s->id);
			}
		}

		// 새로 바뀐 parent의 children들에 대해서 sort_order 조정
		$newSiblings = Department::where('parent_id', '=', $parentId)
							->where('id', '!=', $id)
							->where('sort_order', '>=', $position)
							->get();

		foreach ($newSiblings as $s) {
			$s->sort_order +=1;
			if (!$s->save()) {
				throw new Exception('db failed during updating Department='.$s->id);
			}
		}

		$oldParentId = $Department->parent_id;
		$Department->parent_id = $parentId;
		$Department->sort_order = $position;

		if (!$Department->save()) {
			throw new Exception('db failed during updating Department='.$Department->id);
		}

		DB::commit();

		if ($oldParentId != $parentId) {
			$this->adjustHierarchy($Department->id);
		}
	}


}