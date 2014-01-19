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
                'name'        => '관리자',
                'permissions' => array(
                    'admin' => 1
                )
            ));

            Sentry::createGroup(array(
                'id'=>2,
                'name'        => '사용자',
                'permissions' => array(
                    'admin' => 0
                )
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
