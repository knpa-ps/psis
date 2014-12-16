<?php 

class EqService extends BaseService {

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
			case 'training':
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
			$fileName = $node->node_name.' 캡사이신 희석액 사용내역'; 
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
		$sheet->setCellValue('b1','관서명');
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
			$sheet->setCellValue('c'.($i+1),$rows[$i-1]->user_node->node_name);
			$sheet->setCellValue('d'.($i+1),$rows[$i-1]->type);
			$sheet->setCellValue('e'.($i+1),$rows[$i-1]->location);
			$sheet->setCellValue('f'.($i+1),$rows[$i-1]->event_name);
			$sheet->setCellValue('g'.($i+1),round(($i+1),$rows[$i-1]->amount, 2));
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
			$fileName = $node->node_name.' '.$year.' 캡사이신 희석액 현황'; 
		} else {
			$fileName = $year.' 캡사이신 희석액 현황'; 
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

}