<?php 

class BgMobPayService {

	public function getIntervals() {
		$result = DB::table('bg_mob_pays_interval')->orderBy('start')->get();
		$intervals = array();
		foreach ($result as $row) {
			$intervals[$row->id] = $row;
		}
		return $intervals;
	}


	public function getData($startMonth, $endMonth, $regionId) {
		$query = DB::table('bg_mob_pays_master')
		->where('belong_month','>=',$startMonth.'-01')->where('belong_month','<=',$endMonth.'-01');

		if ($regionId)
		{
			$query->where('dept_id','=',$regionId);
		}

		$data['master'] = $query->select(
				DB::raw('sum(sit_demo+sit_escort+sit_crowd+sit_rescue+sit_etc) sit_sum'),
				DB::raw('sum(sit_demo) sit_demo'),
				DB::raw('sum(sit_escort) sit_escort'),
				DB::raw('sum(sit_crowd) sit_crowd'),
				DB::raw('sum(sit_rescue) sit_rescue'),
				DB::raw('sum(sit_etc) sit_etc'),
				DB::raw('sum(extra_region+extra_office+extra_local+extra_officer_troop+extra_troop) extra_sum'),
				DB::raw('sum(extra_region) extra_region'),
				DB::raw('sum(extra_office) extra_office'),
				DB::raw('sum(extra_local) extra_local'),
				DB::raw('sum(extra_officer_troop) extra_officer_troop'),
				DB::raw('sum(extra_troop) extra_troop'),
				DB::raw('sum(ftn_region+ftn_office+ftn_local+ftn_officer_troop+ftn_troop) ftn_sum'),
				DB::raw('sum(ftn_region) ftn_region'),
				DB::raw('sum(ftn_office) ftn_office'),
				DB::raw('sum(ftn_local) ftn_local'),
				DB::raw('sum(ftn_officer_troop) ftn_officer_troop'),
				DB::raw('sum(ftn_troop) ftn_troop'),
				DB::raw('dept_id')
			)->first();

		$query = DB::table('bg_mob_pays_detail')
		->leftJoin('bg_mob_pays_master', 'bg_mob_pays_master.id','=','bg_mob_pays_detail.master_id')
		->leftJoin('bg_mob_pays_interval', 'bg_mob_pays_interval.id', '=', 'bg_mob_pays_detail.interval_id')
		->where('belong_month','>=',$startMonth.'-01')->where('belong_month','<=',$endMonth.'-01')
		->groupBy('interval_id');

		if ($regionId)
		{
			$query->where('dept_id','=',$regionId);
		}

		$data['detail'] = $query->select(
				'interval_id',
				DB::raw('sum(region+office+local+officer_troop+troop) sum'),
				DB::raw('sum(region) region'),
				DB::raw('sum(office) office'),
				DB::raw('sum(local) local'),
				DB::raw('sum(officer_troop) officer_troop'),
				DB::raw('sum(troop) troop')
			)->get();

		return $data;
	}

	public function insertMobPay($input) {
		$intervals = $this->getIntervals();
		$details = array();
		$ftnSum = array(
				'ftn_region'=>0,
				'ftn_office'=>0,
				'ftn_local'=>0,
				'ftn_officer_troop'=>0,
				'ftn_troop'=>0
				);
		foreach ($intervals as $k=>$i)
		{
			$details[] = array(
					'region'=>$input['ftn_region_'.$k],
					'office'=>$input['ftn_office_'.$k],
					'local'=>$input['ftn_local_'.$k],
					'officer_troop'=>$input['ftn_officer_troop_'.$k],
					'troop'=>$input['ftn_troop_'.$k],
					'interval_id'=>$k
				);
			$ftnSum['ftn_region'] += $input['ftn_region_'.$k]*$i->weight;
			$ftnSum['ftn_office'] += $input['ftn_office_'.$k]*$i->weight;
			$ftnSum['ftn_local'] += $input['ftn_local_'.$k]*$i->weight;
			$ftnSum['ftn_officer_troop'] += $input['ftn_officer_troop_'.$k]*$i->weight;
			$ftnSum['ftn_troop'] += $input['ftn_troop_'.$k]*$i->weight;

		}

		$id = DB::table('bg_mob_pays_master')->insertGetId(
					array_merge(array(
						'belong_month'=>$input['bm'].'-01',
						'dept_id'=>$input['q_region'],
						'sit_demo'=>$input['sit_demo'],
						'sit_escort'=>$input['sit_escort'],
						'sit_crowd'=>$input['sit_crowd'],
						'sit_rescue'=>$input['sit_rescue'],
						'sit_etc'=>$input['sit_etc'],
						'extra_region'=>$input['extra_region'],
						'extra_office'=>$input['extra_office'],
						'extra_local'=>$input['extra_local'],
						'extra_officer_troop'=>$input['extra_officer_troop'],
						'extra_troop'=>$input['extra_troop']
					), $ftnSum)
				);
		foreach ($details as $key=>$val) {
			$details[$key]['master_id'] = $id;
		}

		DB::table('bg_mob_pays_detail')->insert($details);
	}

	public function editMobPay($input) {
		$intervals = $this->getIntervals();

		$belongMonth = $input['bm'];
		$regionId = $input['region'];

		$master = DB::table('bg_mob_pays_master')->select(array('id'))
		->where('belong_month','=',$belongMonth.'-01')
		->where('dept_id','=',$regionId)
		->first();

		if (!$master) {
			return;
		}

		$details = array();
		$ftnSum = array(
				'ftn_region'=>0,
				'ftn_office'=>0,
				'ftn_local'=>0,
				'ftn_officer_troop'=>0,
				'ftn_troop'=>0
				);
		foreach ($intervals as $k=>$i)
		{
			$details[] = array(
					'region'=>$input['ftn_region_'.$k],
					'office'=>$input['ftn_office_'.$k],
					'local'=>$input['ftn_local_'.$k],
					'officer_troop'=>$input['ftn_officer_troop_'.$k],
					'troop'=>$input['ftn_troop_'.$k],
					'interval_id'=>$k,
					'master_id'=>$master->id
				);

			$ftnSum['ftn_region'] += $input['ftn_region_'.$k]*$i->weight;
			$ftnSum['ftn_office'] += $input['ftn_office_'.$k]*$i->weight;
			$ftnSum['ftn_local'] += $input['ftn_local_'.$k]*$i->weight;
			$ftnSum['ftn_officer_troop'] += $input['ftn_officer_troop_'.$k]*$i->weight;
			$ftnSum['ftn_troop'] += $input['ftn_troop_'.$k]*$i->weight;
		}

		DB::table('bg_mob_pays_master')->where('id','=',$master->id)->update(
					array_merge(array(
						'sit_demo'=>$input['sit_demo'],
						'sit_escort'=>$input['sit_escort'],
						'sit_crowd'=>$input['sit_crowd'],
						'sit_rescue'=>$input['sit_rescue'],
						'sit_etc'=>$input['sit_etc'],
						'extra_region'=>$input['extra_region'],
						'extra_office'=>$input['extra_office'],
						'extra_local'=>$input['extra_local'],
						'extra_officer_troop'=>$input['extra_officer_troop'],
						'extra_troop'=>$input['extra_troop']
					), $ftnSum)
				);

		foreach ($details as $d) {
			DB::table('bg_mob_pays_detail')->where('master_id','=',$master->id)->where('interval_id','=',$d['interval_id'])->update($d);
		}
	}
}