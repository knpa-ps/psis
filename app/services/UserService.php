<?php 

class UserService extends BaseService {

	public function register($form) {

		// 유저 생성
		try {
			
			$user = Sentry::createUser(array(
					'activated' => false,
					'account_name' => $form['account_name'],
					'email' => $form['account_name'],
					'password' => $form['password'],
					'user_name' => $form['user_name'],
					'dept_id' => $form['dept_id'],
					'user_rank' => $form['user_rank'],
					'contact' => $form['contact'],
					'contact_phone' => $form['contact_phone'],
					'contact_extension' => $form['contact_extension']
				));

			// 시스템 사용 신청에 따라 사용자 권한 부여
			foreach ($form['groups'] as $system => $group) {

				if ($group == 'none') {
					continue;
				}

				$groupKey = $system.'.'.$group;

				$group = Group::ofKey($groupKey)->first();

				if ($group != null) {
					$user->addGroup($group);
				}

			}

		} catch (Cartalyst\Sentry\Users\UserExistsException $e) {
		    throw new Exception('account name already exists', -1);
		}

	}
	

}