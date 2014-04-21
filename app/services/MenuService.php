<?php 

class MenuService extends BaseService {

	public function activateMenuByUrl($url) {
		$activeMenu = Menu::where('url', '=', $url)->first();
		if ($activeMenu) {
			$this->setActiveMenu($activeMenu->id);
		}
	}

	public function setActiveMenu($id) {
		Session::put('active_menu_id', $id);
	}

	public function getMenuTree($type) {

		$filtered = array();
		
		$user = Sentry::getUser();
		
		$groups = $user->getGroups();
		$groupIds = array();

		foreach ($groups as $g) {
			$groupIds[] = $g->id;
		}

		$isSuperUser = $user->isSuperUser();

		foreach (Menu::ofType($type)->orderBy('sort_order', 'asc')->get() as $menu)  {
			if ($isSuperUser || $menu->visibleTo($groupIds)) {
				$filtered[] = $menu;
			}
		}

		$tree = $this->buildTree($filtered, $type);

		return $tree;
	}

	private function buildTree(array $data, $parentId)
	{
		$tree = array();
		foreach ($data as $menu) {
			if ($menu->parent_id == $parentId) {
				$children = $this->buildTree($data, $menu->id);

				if ($children) {
					$menu->children = $children;
				}

				$menu->is_active = $this->calculateActive($menu);

				$tree[] = $menu;
			}
		}

		return $tree;
	} 

	private function calculateActive(Menu $menu) {

		if (isset($menu->children)) {
			foreach ($menu->children as $c) {
				if ($this->calculateActive($c)) {
					return true;
				}
			}
		}

		if ($menu->id === Session::get('active_menu_id')) {
			return true;
		}

		return false;
	}

	/**
	 * 메뉴의 위치를 변경한다
	 * @param int $id 메뉴 id
	 * @param int $parentId 변경된 위치의 parent id
	 * @param int $position 변경된 위치의 position(같은 레벨의 메뉴들 사이의 0-based index)
	 * @param int $type 변경된 위치의 type
	 */
	public function move($id, $parentId, $position, $type) {

		$menu = Menu::find($id);

		if ($menu === null) {
			throw new Exception('menu not found with id='.$id);
		}

		// 같은 레벨에 있는 메뉴들 중 sort_order가 옮겨진 메뉴보다 나중에 있는 메뉴들에 대해 sort_order - 1
		$oldSiblings = Menu::where('parent_id', '=', $menu->parent_id)
						->where('sort_order', '>', $menu->sort_order)
						->get();

		DB::beginTransaction();

		foreach ($oldSiblings as $s) {
			$s->sort_order -= 1;
			if (!$s->save()) {
				throw new Exception('db failed during updating menu='.$s->id);
			}
		}

		// 새로 바뀐 parent의 children들에 대해서 sort_order 조정
		$newSiblings = Menu::where('parent_id', '=', $parentId)
							->where('id', '!=', $id)
							->where('sort_order', '>=', $position)
							->get();

		foreach ($newSiblings as $s) {
			$s->sort_order +=1;
			if (!$s->save()) {
				throw new Exception('db failed during updating menu='.$s->id);
			}
		}

		$menu->parent_id = $parentId;
		$menu->sort_order = $position;
		$menu->type = $type;

		if (!$menu->save()) {
			throw new Exception('db failed during updating menu='.$menu->id);
		}

		DB::commit();
	}

	/**
	 * 해당 메뉴와 하위 메뉴들을 전부 삭제한다.
	 * @param int $id 삭제할 메뉴 id
	 */
	public function remove($id) {
		$this->doRemove($id);
	}

	private function doRemove($id) {
		$menu = Menu::with('children')->find($id);
		if ($menu === null) {
			throw new Exception('menu not found with id='.$id);
		}

		$children = $menu->children()->get();
		foreach ($children as $child) {
			$this->doRemove($child->id);
		}

		Menu::destroy($id);
	}

	/**
	 * 새 메뉴를 생성한다
	 * @param string $name 메뉴 이름
	 * @param int $parentId 상위 메뉴 아이디
	 * @param int $position 생성될 위치의 position(같은 레벨의 메뉴들 사이의 0-based index)
	 * @param int $type menu type : Menu::ID_VISIBLE_ROOT / Menu::ID_HIDDEN_ROOT
	 * @return Menu 생성된 메뉴 모델
	 */
	public function create($name, $parentId, $position, $type) {

		$menu = new Menu;
		$menu->parent_id = $parentId;
		$menu->name = $name;
		$menu->sort_order = $position;
		$menu->type = $type;

		if ($menu->save()) {
			return $menu;
		} else {
			throw new Exception('db failed');
		}
	}
}