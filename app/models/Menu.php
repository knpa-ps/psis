<?php

class Menu extends Eloquent {
	protected $table = 'menus';

	protected $guarded = array();

	public static $rules = array();

	public function action()
	{
		return $this->hasMany('Action');
	}

	public static function breadcrumbs(array $menus)
	{
		foreach ($menus as $menu)
		{
			if ($menu->is_active)
			{
				if (isset($menu->children))
				{
					$breadcrumbs = self::breadcrumbs($menu->children);
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

	public static function tree()
	{
		$filtered = array();

		$user = Sentry::getUser();
		$groups = $user->getGroups();
		$groupIds = array();
		foreach ($groups as $g)
		{
			$groupIds[] = $g->id;
		}

		$isAdmin = $user->hasAccess('admin');

		foreach (Menu::with('action')->get() as $menu) 
		{
			if (self::isVisible($groupIds, $menu) || $isAdmin)
			{
				$filtered[] = $menu;
			}
		}

		$actionKey = Route::currentRouteAction();
		$tree = self::buildTree($filtered, 0, $actionKey);
		
		return $tree;
	}

	private static function isVisible($groupIds, Menu $menu)
	{
		if (!trim($menu->group_ids))
		{
			return true;
		}

		$alloweds = explode(',', $menu->group_ids);

		$result = array_intersect($groupIds, $alloweds);
		return !empty($result);
	}

	private static function buildTree(array $data, $parentId, $actionKey)
	{
		$tree = array();
		foreach ($data as $menu)
		{
			if ($menu->parent_id == $parentId)
			{
				$children = self::buildTree($data, $menu->id, $actionKey);

				if ($children)
				{
					$menu->children = $children;
				}

				$menu->is_active = self::isActive($menu, $actionKey);
				$menu->href = "#";

				foreach ($menu->action()->get() as $action)
				{
					if ($action->menu_default)
					{
						$menu->href = action($action->action);
					}
				}

				$tree[] = $menu;
			}
		}

		return $tree;
	} 

	private static function isActive(Menu $menu, $actionKey)
	{

		if (isset($menu->children))
		{
			foreach ($menu->children as $c)
			{
				if (self::isActive($c, $actionKey))
				{
					return true;
				}
			}
		}

		foreach ($menu->action()->get() as $action)
		{
			if ($action->action === $actionKey)
			{
				return true;
			}
		}

		return false;
	}
}
