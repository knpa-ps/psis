<?php 

class EqService extends BaseService {

	/**
	 * 사용자에게 허용된 도메인의 카테고리들을 가져온다
	 * @param User $user 
	 * @return Collection<EqCategory>
	 */
	public function getVisibleCategoriesQuery(User $user) {

		$query = EqCategory::with('domain')->orderBy('domain_id', 'asc')
						->orderBy('name', 'asc');

		$visibleDomainIds = $this->getVisibleDomains($user)->fetch('id')->toArray();
		if (count($visibleDomainIds) > 0) {
			$query->whereIn('domain_id', $visibleDomainIds);
		}

		return $query;
	}	

	public function getVisibleDomains(User $user) {
		return EqDomain::all()->filter(function($domain) use ($user) {
			return $user->hasAccess($domain->permission);
		});
	}

}