<?php 

class MenuComposer {
	public function compose($view) {
		$groups = Sentry::getUser()->groups();

		// display
		$routeAction = Route::currentRouteAction();

		$service = new MenuService;

		// 메뉴 트리를 가져온다
		$menus['navbar'] = $service->getMenuTree(Menu::ID_VISIBLE_ROOT);

		$activeMenu = Menu::active()->first();

		if ($activeMenu != null && $activeMenu->type == Menu::ID_HIDDEN_ROOT) {
			$menus['sidebar'] = $service->getMenuTree(Menu::ID_HIDDEN_ROOT);
		} else {
			$menus['sidebar'] = $menus['navbar'];
		}
		

		$view->with(array(
			'menus'=>$menus
		));
	}
}