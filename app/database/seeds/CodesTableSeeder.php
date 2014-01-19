<?php

class CodesTableSeeder extends Seeder {

	public function run()
	{
		DB::transaction(function(){

			DB::table('codes')->delete();
			DB::table('codes_categories')->delete();

			$rankCategory = CodeCategory::create(array(
				'category_code'=>'H001',
				'name'=>'경찰 계급',
				'sort_order'=>0,
				'is_public'=>1
			));

			$rankCodes = array(
				array(
					'category_code'=>'H001',
					'code'=>'R001',
					'title'=>'치안총감',
					'sort_order'=>1
					),
				array(
					'category_code'=>'H001',
					'code'=>'R002',
					'title'=>'치안정감',
					'sort_order'=>2
					),
				array(
					'category_code'=>'H001',
					'code'=>'R003',
					'title'=>'치안감',
					'sort_order'=>3
					),
				array(
					'category_code'=>'H001',
					'code'=>'R004',
					'title'=>'경무관',
					'sort_order'=>4
					),
				array('category_code'=>'H001',
					'code'=>'R005',
					'title'=>'총경',
					'sort_order'=>5
					),
				array('category_code'=>'H001',
					'code'=>'R006',
					'title'=>'경정',
					'sort_order'=>6
					),
				array('category_code'=>'H001',
					'code'=>'R007',
					'title'=>'경감',
					'sort_order'=>7
					),
				array('category_code'=>'H001',
					'code'=>'R008',
					'title'=>'경위',
					'sort_order'=>8
					),
				array('category_code'=>'H001',
					'code'=>'R009',
					'title'=>'경사',
					'sort_order'=>9
					),
				array('category_code'=>'H001',
					'code'=>'R010',
					'title'=>'경장',
					'sort_order'=>10
					),
				array('category_code'=>'H001',
					'code'=>'R011',
					'title'=>'순경',
					'sort_order'=>11
					),
				array('category_code'=>'H001',
					'code'=>'R012',
					'title'=>'의경',
					'sort_order'=>12
					),
				array('category_code'=>'H001',
					'code'=>'R013',
					'title'=>'행정관',
					'sort_order'=>13
					)
			);

			foreach ($rankCodes as $code) {
				Code::create($code);
			}
		});
	}

}
