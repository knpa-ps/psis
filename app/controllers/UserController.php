<?php

class UserController extends BaseController {

	public function isUniqueAccountName()
	{
		$account = Input::get('value');
        $accountNameLabel = Lang::get('labels.login_account_name');
        $validator = Validator::make(array(
	        	$accountNameLabel => $account
		    ),
		    array(
		        $accountNameLabel => 'unique:users,account_name'
		    )
		);

        $result = array("value"=>$account);

		if ($validator->fails())
		{
			$result['valid'] = 0;
			$result['message'] = $validator->messages()->first($accountNameLabel);
		}
		else
		{
			$result['valid'] = 1;
			$result['message'] = '';
		}

		return json_encode($result);
	}
}
