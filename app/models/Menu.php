<?php

class Menu extends Eloquent {

	const ID_VISIBLE_ROOT = 0;
	const ID_HIDDEN_ROOT = 1;

	protected $table = 'menus';

	public function children() {
		return $this->hasMany('Menu', 'parent_id', 'id');
	}

	public function scopeOfType($query, $type) {
		return $query->where('type', '=', $type);
	}

	public function scopeActive($query) {
		return $query->where('id', '=', Session::get('active_menu_id'));
	}

	/**
	 * 메뉴가 group ids들을 갖고 있는 사람에게 보이는 지 판단한다
	 * @param array $groupIds 권한을 검사할 대상 그룹 아이디들
	 * @return boolean
	 */
	public function visibleTo($groupIds) {

		// 만약 그룹 아이디 필드가 비어 있으면 모두 허용
		if (!trim($this->group_ids)) {
			return true;
		}

		$alloweds = explode(',', $this->group_ids);

		$result = array_intersect($groupIds, $alloweds);
		return !empty($result);
	}
}
