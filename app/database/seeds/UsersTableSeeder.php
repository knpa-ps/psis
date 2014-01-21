<?php

class UsersTableSeeder extends Seeder {

	public function run()
	{
        DB::table('users')->truncate();

        // Create the user
        $loginAttribute = Config::get('cartalyst/sentry::users.login_attribute');
        $user = Sentry::createUser(array(
            $loginAttribute     => 'admin',
            'email'=> 'admin',
            'password'  => 'admin',
            'activated' => true,
            'user_name' => '관리자'
        ));

        // Find the group using the group id
        $adminGroup = Sentry::findGroupById(1);

        // Assign the group to the user
        $user->addGroup($adminGroup);
	}

}
