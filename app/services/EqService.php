 <?php

use Carbon\Carbon;

class EqService extends BaseService {



	public function makeCache($nodeId) {

		// 메인 화면에 들어올 때
		// 기존 캐시가 없는 경우
		// item별 보유수량 합계 캐시하기

		$itemCodes = EqItemCode::all();

		foreach ($itemCodes as $c) {
			$items = $c->items;

			foreach ($items as $i) {
				$invSet = EqInventorySet::where('node_id','=',$nodeId)->where('item_id','=',$i->id)->first();
				if ($invSet !== null) {
					$countSum = EqInventoryData::where('inventory_set_id','=',$invSet->id)->get()->sum('count');
					$wreckedSum = EqInventoryData::where('inventory_set_id','=',$invSet->id)->get()->sum('wrecked');
					$acquiredSum = EqItemSupply::whereHas('supplySet', function($q) use ($i) {
						$q->where('item_id','=',$i->id);
					})->where('to_node_id','=',$nodeId)->sum('count');

					Cache::forever('avail_sum_'.$nodeId.'_'.$i->id, $countSum-$wreckedSum);
					Cache::forever('wrecked_sum_'.$nodeId.'_'.$i->id, $wreckedSum);
					Cache::forever('acquired_sum_'.$nodeId.'_'.$i->id, $acquiredSum);
				} else {
					Cache::forever('avail_sum_'.$nodeId.'_'.$i->id, 0);
					Cache::forever('wrecked_sum_'.$nodeId.'_'.$i->id, 0);
					Cache::forever('acquired_sum_'.$nodeId.'_'.$i->id, 0);
				}
			}
		}

		Cache::forever('is_cached_'.$nodeId, 1);
	}

  public function inventorySupply($invData, $value, $isChild){
    $itemId=$invData->parentSet->item_id;
    if($isChild){
      $nodeId=$invData->parentSet->node_id;
      $acquiredBefore=Cache::get('acquired_sum_'.$nodeId.'_'.$itemId);
      $availBefore=Cache::get('avail_sum_'.$nodeId.'_'.$itemId);
      Cache::forever('acquired_sum_'.$nodeId.'_'.$itemId,$acquiredBefore+$value);
      Cache::forever('avail_sum_'.$nodeId.'_'.$itemId, $availBefore+$value);
    }else{
      $nodeId = $invData->parentSet->ownerNode->id;
      $availBefore=Cache::get('avail_sum_'.$nodeId.'_'.$itemId);
      Cache::forever('avail_sum_'.$nodeId.'_'.$itemId, $availBefore+$value);
    }
    $invData->count += $value;
    if (!$invData->save()) {
			return App::abort(500);
		}
  }

	public function inventoryWithdraw($invData, $value, $isChild) {
		//장비를 빼는 기능 및 장비 빼고 음수가 안 나오도록 체크하는 기능을 넣음
		$itemId = $invData->parentSet->item_id;

    if($isChild){
      $nodeId=$invData->parentSet->node_id;
      $acquiredBefore=Cache::get('acquired_sum_'.$nodeId.'_'.$itemId);
      $availBefore=Cache::get('avail_sum_'.$nodeId.'_'.$itemId);
      Cache::forever('acquired_sum_'.$nodeId.'_'.$itemId,$acquiredBefore-$value);
      Cache::forever('avail_sum_'.$nodeId.'_'.$itemId, $availBefore-$value);
      $invData->count -= $value;
      if (!$invData->save()) {
        return App::abort(500);
      }
    }else {
      $nodeId = $invData->parentSet->ownerNode->id;
      $availBefore=Cache::get('avail_sum_'.$nodeId.'_'.$itemId);
      Cache::forever('avail_sum_'.$nodeId.'_'.$itemId, $availBefore-$value);
      if ($invData->count >= $value) {
  			$invData->count -= $value;
  			if (!$invData->save()) {
  				return App::abort(500);
  			}
  		} else {
  			$type = EqItemType::find($invData->item_type_id);
  			throw new Exception($ownerNode->full_name."이 보유한 ".$type->type_name." 수량이 ".($value-$invData->count)."개 부족합니다.");
  		}
    }
	}

	public function getPavaPerMonthData($year, $nodeId) {

		$now = Carbon::now();
		if ($year == null) {
			$year = $now->year;
		}
		$data['node']=EqSupplyManagerNode::find($nodeId);
		$data['year'] = $year;
		$data['nowYear'] = $now->year;
		$data['initYears'] = EqPavaInitHolding::select('year')->distinct()->get();

		$yearInitHolding = EqPavaInitHolding::where('year','=',$year)->where('node_id','=',$nodeId)->first()->amount;
		$data['yearInitHolding'] = $yearInitHolding;

		$events = EqWaterPavaEvent::whereNotNull('pava_amount')->where('node_id','=',$nodeId)->where('date','like',$year.'%')->get();
		$drills = EqPavaIO::where('node_id','=',$nodeId)->where('date','like',$year.'%')->where('sort','=','drill')->get();

		$stock = array();
		$usageSum = array();
		$usageT = array();
		$usageA = array();
		$timesSum = array();
		$timesT = array();
		$timesA = array();
		$lost = array();

		$now = Carbon::now();
		//올해면 아직 안 온 달은 비워둔다.
		$data['presentStock'] = null;

		for ($i=1; $i <= 12; $i++) {

			$firstDayofMonth = Carbon::createFromDate($year, $i, 1, 'Asia/Seoul')->subDay();
			if ($i != 12) {
				$lastDayofMonth = Carbon::createFromDate($year, $i+1, 1, 'Asia/Seoul')->subDay();
			} else {
				$lastDayofMonth = Carbon::createFromDate($year, $i, 31, 'Asia/Seoul');
			}

			$eventsUntilithMonth = EqWaterPavaEvent::whereNotNull('pava_amount')->where('node_id','=',$nodeId)->where('date','like',$year.'%')->where('date','<=',$lastDayofMonth)->get();
			$drillsUntilithMonth = EqPavaIO::where('node_id','=',$nodeId)->where('date','like',$year.'%')->where('date','<=',$lastDayofMonth)->where('sort','=','drill')->get();
			$consumptionUntilithMonth = $eventsUntilithMonth->sum('pava_amount') + $drillsUntilithMonth->sum('amount');

			$lostUntilithMonth = EqPavaIO::where('node_id','=',$nodeId)->where('date','like',$year.'%')->where('date','<=',$lastDayofMonth)->where('sort','=','lost')->sum('amount');
			$stock[$i] = $yearInitHolding - $consumptionUntilithMonth - $lostUntilithMonth;

			$month = 12;
			if ($year == $now->year) {
				$month = $now->month;
				if ($month == $i) {
					$data['presentStock'] = $stock[$i];
				}
			} elseif ($year > $now->year) {
				$stock[$i] = null;
				$usageSum[$i] = null;
				$usageT[$i] = null;
				$usageA[$i] = null;
				$timesSum[$i] = null;
				$timesT[$i] = null;
				$timesA[$i] = null;
				$lost[$i] = null;
				continue;
			}

			if ($month < $i) {
				$stock[$i] = null;
				$usageSum[$i] = null;
				$usageT[$i] = null;
				$usageA[$i] = null;
				$timesSum[$i] = null;
				$timesT[$i] = null;
				$timesA[$i] = null;
				$lost[$i] = null;
				continue;
			}

			$eventsThisMonth = EqWaterPavaEvent::whereNotNull('pava_amount')->where('node_id','=',$nodeId)->where('date','>',$firstDayofMonth)->where('date','<=',$lastDayofMonth)->get();
			$drillsThisMonth = EqPavaIO::where('node_id','=',$nodeId)->where('date','>',$firstDayofMonth)->where('date','<=',$lastDayofMonth)->where('sort','=','drill')->get();
			$lostThisMonth = EqPavaIO::where('node_id','=',$nodeId)->where('date','>',$firstDayofMonth)->where('date','<=',$lastDayofMonth)->where('sort','=','lost')->get();


			$usageT[$i] = $drillsThisMonth->sum('amount');
			$usageA[$i] = $eventsThisMonth->sum('pava_amount');
			$usageSum[$i] = $usageT[$i] + $usageA[$i];

			$timesSum[$i] = $eventsThisMonth->count() + $drillsThisMonth->count();
			$timesT[$i] = $drillsThisMonth->count();
			$timesA[$i] = $eventsThisMonth->count();
			$lost[$i]  = $lostThisMonth->sum('amount');
		}
		$data['stockSum'] = array_sum($stock);
 		$data['usageSumSum'] = array_sum($usageSum);
 		$data['usageTSum'] = array_sum($usageT);
 		$data['usageASum'] = array_sum($usageA);
 		$data['timesSumSum'] = array_sum($timesSum);
 		$data['timesTSum'] = array_sum($timesT);
 		$data['timesASum'] = array_sum($timesA);
 		$data['lostSum'] = array_sum($lost);

		$data['stock'] = $stock;
		$data['usageSum'] = $usageSum;
		$data['usageT'] = $usageT;
		$data['usageA'] = $usageA;
		$data['timesSum'] = $timesSum;
		$data['timesT'] = $timesT;
		$data['timesA'] = $timesA;
		$data['lost'] = $lost;

		return $data;
	}

	public function deleteSupplySet($id) {
		$s = EqItemSupplySet::find($id);
		if (!$s) {
			return 'foo';
		}
		$datas = $s->children;

		$item = $s->item;

		DB::beginTransaction();

		// 보급을 삭제하면서 각 하위관서에 보급했던 수량을 다시 가져온다.

		// 1. 보급한 관서의 인벤토리 수량 더하기
		$supplierNodeId = $s->from_node_id;
    // 보급수량 초기화

		$supplierInvSet = EqInventorySet::where('node_id','=',$supplierNodeId)->where('item_id','=',$item->id)->first();

		foreach ($item->types as $t) {
			$suppliedCount = EqItemSupply::where('supply_set_id','=',$s->id)->where('item_type_id','=',$t->id)->sum('count');
			$invData = EqInventoryData::where('inventory_set_id','=',$supplierInvSet->id)->where('item_type_id','=',$t->id)->first();
      try {
        $this->inventorySupply($invData, $suppliedCount, false);
      } catch (Exception $e) {
        return Redirect::to('equips/supplies')->with('message', $e->getMessage() );
      }
		}

		// 2. 보급받은 관서의 인벤토리 수량 빼기
		foreach ($datas as $d) {
			$itemTypeId = $d->item_type_id;
			$toNodeId = $d->to_node_id;
			$invSet = EqInventorySet::where('node_id','=',$toNodeId)->where('item_id','=',$s->item_id)->first();
			$invData = EqInventoryData::where('inventory_set_id','=',$invSet->id)->where('item_type_id','=',$d->item_type_id)->first();

      try {
        $this->inventoryWithdraw($invData, $d->count, true);
      } catch (Exception $e) {
        return Redirect::to('equips/supplies')->with('message', $e->getMessage() );
      }

			if (!$d->delete()) {
				return App::abort(500);
			}
		}

		if (!$s->delete()) {
			return App::abort(500);
		}

		// 3. 하위 부서의 보급도 취소하기
		$supplierNode = EqSupplyManagerNode::find($supplierNodeId);
		$lowerNodes = $supplierNode->managedChildren;

		if (!$lowerNodes) {
			return;
		}

		// 하위 노드에서 보급한 내역을 찾아 지운다.
		foreach ($lowerNodes as $n) {
			$supSets = EqItemSupplySet::where('item_id','=',$item->id)->where('from_node_id','=',$n->id)->where('created_at','>',$s->created_at)->get();
			foreach ($supSets as $s) {
				$this->deleteSupplySet($s->id);
			}
		}

		DB::commit();

		return 1;
	}

	public function getScopeDept(User $user) {
		if (!$user->isSuperUser() && $user->department->type_code != Department::TYPE_HEAD) {
			// 사용자의 관서 종류에 따라 조회 범위 설정
			if ($user->department->type_code == Department::TYPE_REGION) {
				$scopeRootDept = $user->department->region();
			} else {
				$scopeRootDept = $user->department;
			}
			return $scopeRootDept;
		} else {
			return null;
		}
	}

	public function getEventType($code) {
		switch ($code) {
			case 'assembly':
				$eventType = '집회';
				break;
			case 'drill':
				$eventType = '훈련';
				break;
			default:
				return App::abort(500);
				break;
		}
		return $eventType;
	}

	/**
	 * 사용자에게 허용된 도메인의 카테고리들을 가져온다
	 * @param User $user
	 * @return Collection<EqCategory>
	 */
	public function getVisibleCategoriesQuery(User $user) {

		$query = EqCategory::with('domain')->orderBy('domain_id', 'asc')
						->orderBy('name', 'asc');

		$visibleDomainIds = $this->getVisibleDomains($user)->fetch('id')->toArray();
		if (count($visibleDomainIds) == 0) {
			$visibleDomainIds[] = -1;
		}

		$query->whereIn('domain_id', $visibleDomainIds);
		return $query;
	}

	public function getVisibleDomains(User $user) {
		return EqDomain::all()->filter(function($domain) use ($user) {
			return $user->hasAccess($domain->permission);
		});
	}

	public function getVisibleItemsQuery(User $user) {

		$visibleCategoryIds = $this->getVisibleCategoriesQuery($user)->lists('id');

		if (count($visibleCategoryIds) == 0) {
			$visibleCategoryIds[] = -1;
		}

		$query = EqItem::whereIn('category_id', $visibleCategoryIds)
						->orderBy('category_id', 'asc')
						->orderBy('name', 'asc');
		return $query;
	}

	public function getInventoriesQuery(User $user) {
		$query = EqInventory::query();

		$scope = $this->getScopeDept($user);

		if ($scope) {
			$query->where('full_path', 'like', $scope->full_path.'%');
		}

		return $query;
	}

	public function exportCapsaicinByEvent($rows, $node, $now) {
		//xls obj 생성
		$objPHPExcel = new PHPExcel();
		if (isset($node)) {
			$fileName = $node->node_name.' 사용내역';
		} else {
			$fileName = '캡사이신 희석액 사용내역';
		}
		//obj 속성
		$objPHPExcel->getProperties()
			->setTitle($fileName)
			->setSubject($fileName);
		//셀 정렬(가운데)
		$objPHPExcel->getDefaultStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

		$sheet = $objPHPExcel->setActiveSheetIndex(0);

		$sheet->setCellValue('a1','일자');
		$sheet->setCellValue('b1','관할청');
		$sheet->setCellValue('c1','중대');
		$sheet->setCellValue('d1','행사유형');
		$sheet->setCellValue('e1','사용장소');
		$sheet->setCellValue('f1','행사명');
		$sheet->setCellValue('g1','사용량(ℓ)');
		//양식 부분 끝
		//이제 사용내역 나옴
		for ($i=1; $i <= sizeof($rows); $i++) {
			$sheet->setCellValue('a'.($i+1),$rows[$i-1]->date);
			$sheet->setCellValue('b'.($i+1),$rows[$i-1]->node->node_name);
			$sheet->setCellValue('c'.($i+1),$rows[$i-1]->user_node->full_name);
			$sheet->setCellValue('d'.($i+1),$rows[$i-1]->type);
			$sheet->setCellValue('e'.($i+1),$rows[$i-1]->location);
			$sheet->setCellValue('f'.($i+1),$rows[$i-1]->event_name);
			$sheet->setCellValue('g'.($i+1),round($rows[$i-1]->amount, 2));
		}


		//파일로 저장하기
		$writer = PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel2007');
		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Content-Type: application/force-download");
		header('Content-type: application/vnd.ms-excel');
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Encoding: UTF-8');
		header('Content-Disposition: attachment; filename="'.$fileName.' '.$now.'.xlsx"');
		header("Content-Transfer-Encoding: binary ");
		$writer->save('php://output');
		return;
	}

	public function exportWaterByEvent($rows, $node, $now) {
		//xls obj 생성
		$objPHPExcel = new PHPExcel();
		if (isset($node)) {
			$fileName = $node->node_name.' 물 사용내역';
		} else {
			$fileName = '물 사용내역';
		}
		//obj 속성
		$objPHPExcel->getProperties()
			->setTitle($fileName)
			->setSubject($fileName);
		//셀 정렬(가운데)
		$objPHPExcel->getDefaultStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

		$sheet = $objPHPExcel->setActiveSheetIndex(0);

		$sheet->setCellValue('a1','일자');
		$sheet->setCellValue('b1','관서명');
		$sheet->setCellValue('c1','사용장소');
		$sheet->setCellValue('d1','행사명');
		$sheet->setCellValue('e1','사용량(ton)');
		//양식 부분 끝
		//이제 사용내역 나옴
		for ($i=1; $i <= sizeof($rows); $i++) {
			$sheet->setCellValue('a'.($i+1),$rows[$i-1]->date);
			$sheet->setCellValue('b'.($i+1),$rows[$i-1]->node->node_name);
			$sheet->setCellValue('c'.($i+1),$rows[$i-1]->location);
			$sheet->setCellValue('d'.($i+1),$rows[$i-1]->event_name);
			$sheet->setCellValue('e'.($i+1),round(($i+1),$rows[$i-1]->amount, 2));
		}


		//파일로 저장하기
		$writer = PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel2007');
		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Content-Type: application/force-download");
		header('Content-type: application/vnd.ms-excel');
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Encoding: UTF-8');
		header('Content-Disposition: attachment; filename="'.$fileName.' '.$now.'.xlsx"');
		header("Content-Transfer-Encoding: binary ");
		$writer->save('php://output');
		return;
	}

	public function exportCapsaicinByMonth($data, $node, $now, $year){
		//xls obj 생성
		$objPHPExcel = new PHPExcel();
		if (isset($node)) {
			$fileName = $node->full_name.' '.$year.' 현황';
		} else {
			$fileName = $year.' 현황';
		}
		//obj 속성
		$objPHPExcel->getProperties()
			->setTitle($fileName)
			->setSubject($fileName);
		//셀 정렬(가운데)
		$objPHPExcel->getDefaultStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

		$sheet = $objPHPExcel->setActiveSheetIndex(0);
		$sheet->mergeCells('a1:a3');
		$sheet->mergeCells('b1:c1');
		$sheet->mergeCells('d1:f1');
		$sheet->mergeCells('g1:i1');
		$sheet->mergeCells('j1:j2');
		$sheet->mergeCells('k1:k2');

		for ($i=1; $i <=12 ; $i++) {
			$sheet->mergeCells('b'.($i+3).':c'.($i+3));
		}

		$sheet->setCellValue('a1','구분');
		$sheet->setCellValue('b1','보유량(ℓ)');
		$sheet->setCellValue('d1','사용량(ℓ)');
		$sheet->setCellValue('g1','사용횟수');
		$sheet->setCellValue('j1','추가량(ℓ)');
		$sheet->setCellValue('k1','불용량(ℓ)');
		$sheet->setCellValue('b2','현재보유량(ℓ)');
		$sheet->setCellValue('c2','최초보유량(ℓ)');
		$sheet->setCellValue('d2','계');
		$sheet->setCellValue('e2','훈련시');
		$sheet->setCellValue('f2','집회 시위시');
		$sheet->setCellValue('g2','계');
		$sheet->setCellValue('h2','훈련시');
		$sheet->setCellValue('i2','집회 시위시');
		$sheet->setCellValue('b3',isset($data['presentStock']) ? round($data['presentStock'], 2) : '');
		$sheet->setCellValue('c3',round($data['firstDayHolding'], 2));
		$sheet->setCellValue('d3',round($data['usageSumSum'], 2));
		$sheet->setCellValue('e3',round($data['usageTSum'], 2));
		$sheet->setCellValue('f3',round($data['usageASum'], 2));
		$sheet->setCellValue('g3',$data['timesSumSum']);
		$sheet->setCellValue('h3',$data['timesTSum']);
		$sheet->setCellValue('i3',$data['timesASum']);
		$sheet->setCellValue('j3',round($data['additionSum'], 2));
		$sheet->setCellValue('k3',round($data['discardSum'], 2));
		//양식 부분 끝
		//이제 월별 자료 나옴

		for ($i=1; $i <=12 ; $i++) {
			$sheet->setCellValue('A'.($i+3), $i.'월');
			if (isset($data['stock'][$i])) {
				$sheet->setCellValue('B'.($i+3), round($data['stock'][$i], 2) );
				$sheet->setCellValue('D'.($i+3), round($data['usageSum'][$i], 2) );
				$sheet->setCellValue('E'.($i+3), round($data['usageT'][$i], 2) );
				$sheet->setCellValue('F'.($i+3), round($data['usageA'][$i], 2) );
			}

			$sheet->setCellValue('G'.($i+3), $data['timesSum'][$i] );
			$sheet->setCellValue('H'.($i+3), $data['timesT'][$i] );
			$sheet->setCellValue('I'.($i+3), $data['timesA'][$i] );
			$sheet->setCellValue('J'.($i+3), round($data['addition'][$i], 2) );
			$sheet->setCellValue('K'.($i+3), round($data['discard'][$i], 2) );

		}

		//파일로 저장하기
		$writer = PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel2007');
		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Content-Type: application/force-download");
		header('Content-type: application/vnd.ms-excel');
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Encoding: UTF-8');
		header('Content-Disposition: attachment; filename="'.$fileName.' '.$now.'.xlsx"');
		header("Content-Transfer-Encoding: binary ");
		$writer->save('php://output');
	}

	public function exportGeneralTable($node) {

		$now = Carbon::now();
		$itemTotalNum = 0;

		$categories = EqCategory::where('domain_id','=',1)->get();

		$objPHPExcel = new PHPExcel();
		$fileName = '집회시위 관리장비 점검 총괄표('.$node->full_name.')';

		//obj 속성
		$objPHPExcel->getProperties()
			->setTitle($fileName)
			->setSubject($fileName);
		//셀 정렬(가운데)
		$objPHPExcel->getDefaultStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

		$sheet = $objPHPExcel->setActiveSheetIndex(0);

		//양식 만들기
		$sheet->mergeCells('a1:a2');
		$sheet->setCellValue('a1', '기관명');
		$sheet->mergeCells('b1:d2');
		$sheet->setCellValue('b1', '장비명');

		//총계 열 추가
		$lastColIdx = PHPExcel_Cell::columnIndexFromString($sheet->getHighestDataColumn());

		$sheet->mergeCells(PHPExcel_Cell::stringFromColumnIndex($lastColIdx).'1:'.PHPExcel_Cell::stringFromColumnIndex($lastColIdx+2).'1');
		$sheet->setCellValue(PHPExcel_Cell::stringFromColumnIndex($lastColIdx).'1', '총계');
		$sheet->setCellValue(PHPExcel_Cell::stringFromColumnIndex($lastColIdx).'2','보급');
		$sheet->setCellValue(PHPExcel_Cell::stringFromColumnIndex($lastColIdx+1).'2','파손');
		$sheet->setCellValue(PHPExcel_Cell::stringFromColumnIndex($lastColIdx+2).'2','사용가능');

		//4년이상 초과 열
		$lastColIdx = PHPExcel_Cell::columnIndexFromString($sheet->getHighestDataColumn());

		$sheet->mergeCells(PHPExcel_Cell::stringFromColumnIndex($lastColIdx).'1:'.PHPExcel_Cell::stringFromColumnIndex($lastColIdx+2).'1');
		$sheet->setCellValue(PHPExcel_Cell::stringFromColumnIndex($lastColIdx).'1', $now->subYears(4)->year.'년 이전');
		$sheet->setCellValue(PHPExcel_Cell::stringFromColumnIndex($lastColIdx).'2','보급');
		$sheet->setCellValue(PHPExcel_Cell::stringFromColumnIndex($lastColIdx+1).'2','파손');
		$sheet->setCellValue(PHPExcel_Cell::stringFromColumnIndex($lastColIdx+2).'2','사용가능');

		//4개년 열 추가
		for ($i=0; $i <= 3; $i++) {
			$lastColIdx = PHPExcel_Cell::columnIndexFromString($sheet->getHighestDataColumn());

			$sheet->mergeCells(PHPExcel_Cell::stringFromColumnIndex($lastColIdx).'1:'.PHPExcel_Cell::stringFromColumnIndex($lastColIdx+2).'1');
			$sheet->setCellValue(PHPExcel_Cell::stringFromColumnIndex($lastColIdx).'1', $now->addYear()->year.'년');

			$sheet->setCellValue(PHPExcel_Cell::stringFromColumnIndex($lastColIdx).'2','보급');
			$sheet->setCellValue(PHPExcel_Cell::stringFromColumnIndex($lastColIdx+1).'2','파손');
			$sheet->setCellValue(PHPExcel_Cell::stringFromColumnIndex($lastColIdx+2).'2','사용가능');
		}

		$threeYearsAgo = Carbon::now()->subYears(3)->firstOfYear();

		//장비별 행, 행별 자료 입력
		foreach ($categories as $c) {

			$itemsInCategory = EqItemCode::where('category_id','=',$c->id)->get();
			$itemTotalNum += sizeof($itemsInCategory);
			$lastRow = $sheet->getHighestRow();
			$sheet->setCellValue('b'.($lastRow+1), $c->name);
			$sheet->mergeCells('b'.($lastRow+1).':b'.($lastRow+sizeof($itemsInCategory)));


			for ($i=1; $i<=sizeof($itemsInCategory) ; $i++) {
				$sheet->mergeCells('c'.($lastRow+$i).':d'.($lastRow+$i));
				$sheet->setCellValue('c'.($lastRow+$i), $itemsInCategory[$i-1]->title);

				//TODO
				//총괄표 양식에 자료 넣기

				$itemCode = $itemsInCategory[$i-1];
				$items = $itemCode->items;

				//supply의 target이 node인것들 합

				$availSum = 0;
				$wreckedSum = 0;
				$acquiredSum = 0;
				$availSumBefore4years = 0;
				$wreckedSumBefore4years = 0;
				$acquiredSumBefore4years = 0;

				foreach ($items as $item) {
					$acquiredSum += Cache::get('acquired_sum_'.$node->id.'_'.$item->id);
					$wreckedSum += Cache::get('wrecked_sum_'.$node->id.'_'.$item->id);
					$availSum += Cache::get('avail_sum_'.$node->id.'_'.$item->id);

					if ($item->acquired_date < $threeYearsAgo) {
						$acquiredSumBefore4years += Cache::get('acquired_sum_'.$node->id.'_'.$item->id);
						$wreckedSumBefore4years += Cache::get('wrecked_sum_'.$node->id.'_'.$item->id);
						$availSumBefore4years += Cache::get('avail_sum_'.$node->id.'_'.$item->id);
					}

				}

				//총 지급수량
				$sheet->setCellValue('e'.($lastRow+$i), $acquiredSum);
				//총 파손수량
				$sheet->setCellValue('f'.($lastRow+$i), $wreckedSum);
				//총 가용수량
				$sheet->setCellValue('g'.($lastRow+$i), $availSum);

				//supply의 target이 node인 것 중 supplied date가 4년 이전인것
				$sheet->setCellValue('h'.($lastRow+$i), $acquiredSumBefore4years);
				$sheet->setCellValue('i'.($lastRow+$i), $wreckedSumBefore4years);
				$sheet->setCellValue('j'.($lastRow+$i), $availSumBefore4years);

				for ($j=0; $j <=3 ; $j++) {
					$ColIdx = 10+3*$j;
					// TODO
					// 연도별 수량 입력할 곳
					$year = $threeYearsAgo->year + $j;
					$lastDayOfLastYear = Carbon::parse('last day of December '.($year-1));
					$firstDayOfNextYear = Carbon::parse('first day of January '.($year+1));

					$acquiredSumInYear=0;
					$wreckedSumInYear=0;
					$availSumInYear=0;

					foreach($items as $item){
						if($lastDayOfLastYear < $item->acquired_date && $item->acquired_date < $firstDayOfNextYear){
							$acquiredSumInYear += Cache::get('acquired_sum_'.$node->id.'_'.$item->id);
							$wreckedSumInYear += Cache::get('wrecked_sum_'.$node->id.'_'.$item->id);
							$availSumInYear += Cache::get('avail_sum_'.$node->id.'_'.$item->id);
						}
					}

					$sheet->setCellValue(PHPExcel_Cell::stringFromColumnIndex($ColIdx).($lastRow+$i), $acquiredSumInYear);
					$sheet->setCellValue(PHPExcel_Cell::stringFromColumnIndex($ColIdx+1).($lastRow+$i), $wreckedSumInYear);
					$sheet->setCellValue(PHPExcel_Cell::stringFromColumnIndex($ColIdx+2).($lastRow+$i), $availSumInYear);
				}
			}
		}
		$sheet->setCellValue('a3', $node->node_name);
		$sheet->mergeCells('a3:a'.($itemTotalNum+2));

		//파일로 저장하기
		$writer = PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel2007');
		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Content-Type: application/force-download");
		header('Content-type: application/vnd.ms-excel');
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Encoding: UTF-8');
		header('Content-Disposition: attachment; filename="'.$fileName.' '.$now.'.xlsx"');
		header("Content-Transfer-Encoding: binary ");
		$writer->save('php://output');
	}
}
