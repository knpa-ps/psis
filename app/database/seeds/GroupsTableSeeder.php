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
                    'superuser'=>1
                )
            ));

            Sentry::createGroup(array(
                'id'=>2,
                'name'=> '경비속보 관리자',
                'permissions'=>array(
                    'reports.*'=>1
                )
            ));

            Sentry::createGroup(array(
                'id'=>3,
                'name'=> '경비속보',
                'permissions'=>array(
                    'reports.read'=>1,
                    'reports.create'=>1,
                    'reports.update'=>1
                )
            ));
            
            Sentry::createGroup(array(
                'id'=>4,
                'name'=> '경비예산 관리자',
                'permissions'=>array(
                    'budget.*'=>1
                )
            ));

            Sentry::createGroup(array(
                'id'=>5,
                'name'=> '경비예산-동원급식비',
                'permissions'=>array(
                    'budget.mealpay.create'=>1,
                    'budget.mealpay.read'=>1,
                    'budget.mealpay.delete'=>1
                )
            ));

            Sentry::createGroup(array(
                'id'=>6,
                'name'=> '경비예산-경비동원수당',
                'permissions'=>array(
                    'budget.mob.create'=>1,
                    'budget.mob.read'=>1,
                    'budget.mob.delete'=>1
                )
            ));

            Sentry::createGroup(array(
                'id'=>7,
                'name'=> '지방청 관리자',
                'permissions'=>array(
                    'admin'=>1,
                    'budget.*'=>1,
                    'reports.*'=>1
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
