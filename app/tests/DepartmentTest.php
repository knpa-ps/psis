<?php

class DepartmentTest extends TestCase {

	public function setup() {
		parent::setup();

		Department::truncate();

		$depts = array(
				array(
					'id' => 12,
					'is_alive'=>1,
					'parent_id' => null,
					'dept_name' => 'root2'
					),
				array(
					'id' => 113,
					'is_alive'=>1,
					'parent_id' => 12,
					'dept_name' => '본청2',
					),
				array(
					'id' => 11,
					'is_alive'=>1,
					'parent_id' => null,
					'dept_name' => 'root'
					),
				array(
					'id' => 1,
					'is_alive'=>1,
					'parent_id' => 11,
					'dept_name' => '본청',
					),
				array(
					'id' => 2,
					'is_alive'=>1,
					'parent_id' => 1,
					'dept_name' => '경비국'
					),
				array(
					'id' => 3,
					'is_alive'=>1,
					'parent_id' => 1,
					'dept_name' => '교통국'
					),
				array(
					'id' => 4,
					'is_alive'=>1,
					'parent_id' => 2,
					'dept_name' => '경비과'
					),
				array(
					'id' => 5,
					'is_alive'=>1,
					'parent_id' => 2,
					'dept_name' => '항공과'
					),

				array(
					'id' => 6,
					'is_alive'=>1,
					'parent_id' => 11,
					'dept_name' => '서울청'
					),
				array(
					'id' => 7,
					'is_alive'=>1,
					'parent_id' => 6,
					'dept_name' => '경비'
					),
				array(
					'id' => 8,
					'is_alive'=>1,
					'parent_id' => 6,
					'dept_name' => '생안'
					),
				array(
					'id' => 9,
					'is_alive'=>1,
					'parent_id' => 6,
					'dept_name' => '생안과'
					)
			);

		Department::insert($depts);
		$service = new DepartmentService;
		$service->adjustHierarchy();
	}

	public function testIsAncestor()
	{
		$ancestor = Department::find(1);

		$this->assertTrue($ancestor->isAncestor(4));
		$this->assertTrue(!$ancestor->isAncestor(9));
	}

	public function testAdjustPosition() {

		$service = new DepartmentService;
		$service->adjustPositions();
	}
}