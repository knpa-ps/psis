<?php

class BgMobPayController extends BaseController {

	private $service;

	public function __construct() {
		$this->service = new BgMobPayService;
	}

	public function show()
	{
		$user = Sentry::getUser();
		if (!$user->hasAccess('budget.mobpay.read'))
		{
			return App::abort(404, 'unauthorized action');
		}

		$startMonth = Input::get('q_bm_start') ? Input::get('q_bm_start') : date('Y-m');
		$endMonth = Input::get('q_bm_end') ? Input::get('q_bm_end') : date('Y-m');
		$regionId = Input::get('q_region');
		$regionName = "전체";


		if ($user->hasAccess('budget.admin'))
		{
			$region = Department::regions()->toArray();
			if ($regionId) {
				foreach ($region as $r)
				{
					if ($r['id'] == $regionId)
					{
						$regionName = $r['dept_name'];
					}
				}
			}
		}
		else
		{
			$region = Department::region($user->dept_id);
			
			if ($regionId != $region['id']) {
				return App::abort(404, 'unauthorized action');
			}

			$regionName = $region['dept_name'];

		}

		$data = $this->service->getData($startMonth, $endMonth, $regionId);

		$intervals = $this->service->getIntervals();


        return View::make('budget.mobpay', get_defined_vars());
	}

	public function insert() {
		$user = Sentry::getUser();
		if (!$user->hasAccess('budget.mobpay.create'))
		{
			return App::abort(404, 'unauthorized action');
		}

		$input = Input::all();

		$res = DB::table('bg_mob_pays_master')->where('belong_month','=',Input::get('bm').'-01')->where('dept_id','=',Input::get('q_region'))->get();
		if (count($res) > 0) {
			return -1;
		}

		$this->service->insertMobPay($input);
		return 0;
	}

	public function edit() {
		$user = Sentry::getUser();
		if (!$user->hasAccess('budget.mobpay.edit'))
		{
			return App::abort(404, 'unauthorized action');
		}

		$this->service->editMobPay(Input::all());
		return 0;
	}
}
