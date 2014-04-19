<?php 

class NotificationComposer {

	public function compose($view)
	{
		$view->with(array(
			'message'=> Session::get('message')
		));
	}
}