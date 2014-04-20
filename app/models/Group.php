<?php

class Group extends Cartalyst\Sentry\Groups\Eloquent\Group {
	
	public function scopeDefaults($query) {
		return $query->where('default', '=', 1);
	}

	public function scopeOfKey($query, $key) {
		return $query->where('key', '=', $key);
	}
}
