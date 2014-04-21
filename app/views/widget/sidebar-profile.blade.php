<div class="row">
	<div class="col-xs-12 well">
		<p class=""><span class="glyphicon glyphicon-user"></span> {{ $user->rank->title or '' }} <b>{{ $user->user_name or '' }}</b></p>
		<p><small><b><span class="glyphicon glyphicon-map-marker"></span> 관서</small></b></p>
		<p><small>{{ $dept->full_name or '' }}</small></p>
		<p><b><small><span class="glyphicon glyphicon-cog"></span> 권한그룹</small></b></p>
		<p><small>
			<ul class="list-unstyled">
				@foreach ($groups as $group)
					<li>{{ $group->name }}</li>
				@endforeach
			</ul>
		</small></p>
		<p>
			<a href="{{ action('UserController@showProfile') }}" class="btn btn-primary btn-xs">
				<span class="glyphicon glyphicon-expand"></span> 더보기
			</a>
			<a href="#" id="logout" class="btn btn-primary btn-xs"><span class="glyphicon glyphicon-log-out"></span> 로그아웃</a>
		</p>
	</div>
</div>