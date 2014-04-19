<?php

class DepartmentTest extends TestCase {

	public function setup() {
		parent::setup();

		Department::truncate();

		$depts = array(
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
		DepartmentService::adjustHierarchy();
	}

	public function testIsAncestor()
	{
		$ancestor = Department::find(1);

		$this->assertTrue($ancestor->isAncestor(4));
		$this->assertTrue(!$ancestor->isAncestor(9));
	}

	public function testSetHead() {
		DepartmentService::setHead(3);
		DepartmentService::setHead(2);
		$dept = Department::find(2);
		$this->assertTrue($dept->is_head === 1);
		$dept2 = Department::find(3);
		$this->assertTrue($dept2->is_head === 0);

		DepartmentService::setHead(4);
		$dept = Department::find(4);
		$this->assertTrue($dept->is_head === 1);
	}
}