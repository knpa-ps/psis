<?php

class Menu extends Eloquent {
	protected $table = 'menus';

	protected $guarded = array();

	public static $rules = array();

	public function action()
	{
		return $this->belongsTo('Action', 'action_id', 'id');
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

	/**
	 * 해당 group ids에게 노출된 메뉴들의 트리를 가져온다.
	 * @param  array $groupIds
	 * @return array
	 */
	public static function tree(array $groupIds)
	{
		$filtered = array();

		foreach (Menu::with('action')->get() as $menu) 
		{
			if (self::isVisible($groupIds, $menu))
			{
				$filtered[] = $menu;
			}
		}
		$actionKey = Route::currentRouteAction();
		$tree = self::buildTree($filtered, 0, $actionKey);
		
		return $tree;
	}

	private static function hasActiveDecendant($children, $actionKey)
	{
		foreach ($children as $c)
		{
			if (isset($c->children))
			{
				if (self::hasActiveDecendant($c->children, $actionKey))
				{
					return true;
				}
			}

			if ($c->action->action === $actionKey)
			{
				return true;
			}
		}

		return false;
	}

	private static function isVisible(array $groupIds, Menu $menu)
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

		if ($menu->action->action === $actionKey)
		{
			return true;
		}

		return false;
	}
}
