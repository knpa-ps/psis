<?php

class Department extends Eloquent {

	protected $table = 'departments';

	protected $guarded = array();

	/**
	 * 직속 상위부서
	 * @return Builder
	 */
	public function parent() {
		return $this->belongsTo('Department', 'parent_id', 'id');
	}

	/**
	 * 하위 부서 목록
	 * @return Builder
	 */
	public function children() {
		return $this->hasMany('Department', 'parent_id', 'id');
	}

	public function siblings() {
		return Department::where('parent_id', '=', $this->parent_id);
	}

	/**
	 * 소속 사용자 목록
	 * @return Builder
	 */
	public function members() {
		return $this->hasMany('User', 'dept_id', 'id');
	}

	/**
	 * 이 부서가 $descendantId에 해당하는 부서의 상위부서인지를 검사함
	 * @param int $descendantId 하위부서 아이디
	 * @return boolean 상위부서 여부
	 */
	public function isAncestor($descendantId) {

		$target = Department::find($descendantId);

		if ($target === null) {
			return false;
		}

		$targetPath = $target->full_path;

		return !(strpos($targetPath, ":{$this->id}:") === false);
	}

	/**
	 * 해당 부서가 속한 지방청에 대한 Department 모델을 불러온다.
	 * @return Department 지방청
	 */
	public function region() {
		$paths = explode(':', trim($this->full_path, ':'));

		if (count($paths == 0)) {
			return null;
		}

		return Department::find($paths[0]);
	}

	/**
	 * 지방청 목록
	 * @return Collection<Dpeartment> 지방청들 
	 */
	public function scopeRegions($query) {
		return $query->where('parent_id', '=', 0);
	}

	public function scopeAlive($query) {
		return $query->where('is_alive', '=', 1);
	}
}
