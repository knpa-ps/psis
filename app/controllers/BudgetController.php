<?php

class BudgetController extends \BaseController {

	public function displayDashboard() {
		return View::make('budget.dashboard');
	}
}