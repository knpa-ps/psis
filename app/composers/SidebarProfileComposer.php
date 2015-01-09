<?php 

class SidebarProfileComposer {

	public function compose($view) {
		$user = Sentry::getUser();
		$dept = $user->department;
		$groups = $user->groups()->get();


		//사용자가 장비 관리자이면..
		//장비 관리자 그룹 중 하나에 있을 것이고..
		//SEMS 접근 permission을 가질 것이므로..
		if ($user->hasAccess('equip.*')) {
			$node = $user->supplyNode;
			//산하 현원 총계
			$personnelSum = EqSupplyManagerNode::where('full_path','like',$node->full_path.'%')->sum('personnel');
			//산하 정원 총계 
			$capacitySum = EqSupplyManagerNode::where('full_path','like',$node->full_path.'%')->sum('capacity');	

			$view->with(array(
				'user' => $user,
				'dept' => $dept,
				'groups' => $groups,
				'personnelSum' => $personnelSum,
				'capacitySum' => $capacitySum
			));
		} else {
			$view->with(array(
				'user' => $user,
				'dept' => $dept,
				'groups' => $groups
			));
		}
	}
	
}

