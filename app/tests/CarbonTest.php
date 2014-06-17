<?php 
use Carbon\Carbon;

class CarbonTest extends TestCase {

	public function testConstruction() {
		$carbon = new Carbon('2014-04-03 12:00:00');
		$carbon2 = new Carbon('2014-04-04 18:30:00');
		echo $carbon->diffInMinutes($carbon2) / 60;
	}

}