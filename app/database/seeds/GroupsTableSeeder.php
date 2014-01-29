<?php

class GroupsTableSeeder extends Seeder {

	public function run()
	{
        DB::table('groups')->truncate();
        try
        {
            // Create the group
            Sentry::createGroup(array(
                'id'=>1,
                'name'        => '최고관리자',
                'permissions' => array(
                    'admin' => 1,
                    'superuser'=>1
                )
            ));

            Sentry::createGroup(array(
                'id'=>2,
                'name'=> '사용자'
            ));

            Sentry::createGroup(array(
                'id'=>3,
                'name'=> '경비상황보고 관리자'
            ));

            Sentry::createGroup(array(
                'id'=>4,
                'name'=>'경비상황보고'
            ));

        }
        catch (Cartalyst\Sentry\Groups\NameRequiredException $e)
        {
            echo 'Name field is required';
        }
        catch (Cartalyst\Sentry\Groups\GroupExistsException $e)
        {
            echo 'Group already exists';
        }
	}

}
