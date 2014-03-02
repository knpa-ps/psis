<div class="row-fluid">
	<div class="span12">
		<ul class="nav nav-pills">
			<?php $currentAction = Route::currentRouteAction(); ?>
			<li 
				{{ $currentAction=='BgMobController@show'?'class="active"':'' }} 
			>
				<a href="{{action('BgMobController@show')}}">지급내역</a>
			</li>
			<li 
				{{ $currentAction=='BgMobController@showSitStat'?'class="active"':'' }}
			>
				<a href="{{action('BgMobController@showSitStat')}}">상황별 동원인원</a>
			</li>
		</ul>
	</div>
</div>