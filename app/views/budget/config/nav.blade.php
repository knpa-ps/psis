<div class="row-fluid">
	<div class="span12">
		<ul class="nav nav-pills">
			<?php $currentAction = Route::currentRouteAction(); ?>
			<li 
				{{ $currentAction=='BgConfigController@show'?'class="active"':'' }} 
			>
				<a href="{{action('BgConfigController@show')}}">동원급식비</a>
			</li>
			<li 
				{{ $currentAction=='BgConfigController@showMob'?'class="active"':'' }}
			>
				<a href="{{action('BgConfigController@showMob')}}">경비동원수당</a>
			</li>
		</ul>
	</div>
</div>