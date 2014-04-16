<div class="row-fluid">
	<div class="span12">
		<ul class="nav nav-pills">
			<?php $currentAction = Route::currentRouteAction(); ?>
			<li 
				{{ $currentAction=='BgMealPayController@show'?'class="active"':'' }} 
			>
				<a href="{{action('BgMealPayController@show')}}">지급내역</a>
			</li>
			<li 
				{{ $currentAction=='BgMealPayController@showSitStat'?'class="active"':'' }}
			>
				<a href="{{action('BgMealPayController@showSitStat')}}">동원상황별 통계</a>
			</li>
		</ul>
	</div>
</div>