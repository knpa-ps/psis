<?php

class ManageController extends \BaseController {

	public function displayDashboard() {
		return View::make('manage.dashboard');
	}

	public function displayReportUsers(){
		return View::make('manage.report-users');
	}

	public function displayBudgetUsers(){
		return View::make('manage.budget-users');
	}
}