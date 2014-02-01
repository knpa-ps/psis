<?php

class BgMealPayController extends BaseController {

	/**
	 * @var BgMealPayService
	 */
	private $service;

	public function __construct() {
		$this->service = new BgMealPayService;
	}

	public function show()
	{
		$user = Sentry::getUser();
		if (!$user->hasAccess('budget.mealpay.read'))
		{
			return App::abort(404, 'unauthorized action');
		}

		if ($user->hasAccess('budget.admin'))
		{
			$region = Department::regions()->toArray();
		}
		else
		{
			$region = Department::region($user->dept_id);
		}

		$configs = PSConfig::category('budget.mealpay');

		$editableStart = $this->service->getEditableDateStart();
		return View::make('budget.mealpay', array(
				'region'=>$region,
				'user'=>$user,
				'configs' => $configs,
				'editableStart'=>$editableStart
		));
	}

	public function read()
	{
		$user = Sentry::getUser();
		if (!$user->hasAccess('budget.mealpay.read'))
		{
			return App::abort(404, 'unauthorized action');
		}

		$start = Input::get('q_date_start');
		$end = Input::get('q_date_end');
		$region = Input::get('q_region');
		$groupByMonth = Input::get('q_monthly_sum');
		$event = Input::get("q_event");
		$query = $this->service->buildQuery($start, $end, $region, $event, $groupByMonth);

		return Datatables::of($query)->make();
	}

	public function create()
	{
		if (!Sentry::getUser()->hasAccess('budget.mealpay.create'))
		{
			return App::abort(404, 'unauthorized action');
		}

		$input = Input::all();
		$user = Sentry::getUser();
		$region = Department::region($user->dept_id);
		$region = null;
		if ($region != null)
		{
			$deptId = $region->id;
		}
		else
		{
			$deptId = 1;
		}
		$input['dept_id'] = $deptId;
		$input['creator_id'] = $user->id;

		return $this->service->create($input);
	}

	public function delete()
	{
		if (!Sentry::getUser()->hasAccess('budget.mealpay.delete'))
		{
			return App::abort(404, 'unauthorized action');
		}

		$ids = Input::all();
		if (count($ids) == 0)
		{
			return 0;
		}

		return $this->service->delete($ids);
	}
}
