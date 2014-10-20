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

	public function getItems($domainId) {


	}
}