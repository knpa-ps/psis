<?php 

class MenuService {

	public function getActiveMenu()
	{
		return Menu::where('id', '=', Session::get('activeMenuId'))->first();
	}

	public function setActiveMenu($action)
	{
		if ($action == 'HomeController@showDashboard')
		{
			Session::put('activeMenuId', null);	
			return;
		}

		$menu = Menu::where('action', '=', $action)->first();
		if ($menu)
		{
			Session::put('activeMenuId', $menu->id);
		}
	}

	public function breadcrumbs(array $menus)
	{
		foreach ($menus as $menu)
		{
			if ($menu->is_active)
			{
				if (isset($menu->children))
				{
					$breadcrumbs = $this->breadcrumbs($menu->children);
				}
				else
				{
					$breadcrumbs = array();
				}
				array_unshift($breadcrumbs, $menu);
				return $breadcrumbs;
			}
		}

		return array();
	}

	public function getMenuTree()
	{
		$filtered = array();
		$user = Sentry::getUser();
		$groups = $user->getGroups();
		$groupIds = array();
		foreach ($groups as $g)
		{
			$groupIds[] = $g->id;
		}

		$isSuperUser = $user->isSuperUser();

		foreach (Menu::all() as $menu) 
		{
			if ($this->isVisible($groupIds, $menu) || $isSuperUser)
			{
				$filtered[] = $menu;
			}
		}

		$activeMenuId = Session::get('activeMenuId');
		$tree = $this->buildTree($filtered, 0, $activeMenuId);
		
		return $tree;
	}

	private function isVisible($groupIds, Menu $menu)
	{
		if (!trim($menu->group_ids))
		{
			return true;
		}

		$alloweds = explode(',', $menu->group_ids);

		$result = array_intersect($groupIds, $alloweds);
		return !empty($result);
	}

	private function buildTree(array $data, $parentId, $activeMenuId)
	{
		$tree = array();
		foreach ($data as $menu)
		{
			if ($menu->parent_id == $parentId)
			{
				$children = $this->buildTree($data, $menu->id, $activeMenuId);

				if ($children)
				{
					$menu->children = $children;
				}

				$menu->is_active = $this->isActive($menu, $activeMenuId);
				$menu->href = "#";

				if ($menu->is_shortcut)
				{
					$menu->href = $menu->url;
				}
				else
				{
					if (trim($menu->action))
					{
						$menu->href = action($menu->action);
					}
				}

				$tree[] = $menu;
			}
		}

		return $tree;
	} 

	private function isActive(Menu $menu, $activeMenuId)
	{

		if (isset($menu->children))
		{
			foreach ($menu->children as $c)
			{
				if ($this->isActive($c, $activeMenuId))
				{
					return true;
				}
			}
		}

		if ($menu->id === $activeMenuId)
		{
			return true;
		}

		return false;
	}
}