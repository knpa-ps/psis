<div class="row">
	<div class="col-xs-12 well">
		<p class="">
			<a href="{{ action('UserController@displayProfile') }}" class="black">
			<span class="glyphicon glyphicon-user"></span> {{ $user->rank->title or '' }} <b>{{ $user->user_name or '' }}</b>
			</a>
		</p>
		<p><small><b><span class="glyphicon glyphicon-map-marker"></span> 소속관서</small></b></p>
		<p><small>{{ $dept->full_name or '' }}</small></p>
		<p><b><small><span class="glyphicon glyphicon-cog"></span> 권한그룹</small></b></p>
		<p><small>
			<ul class="list-unstyled">
				@foreach ($groups as $group)
					<li>{{ $group->name }}</li>
				@endforeach
			</ul>
		</small></p>
		@if ($user->hasAccess("eq.*"))
		<p><small><b><span class="glyphicon glyphicon-home"></span> 현원/정원</small></b></p>
		<p><small>
			<ul class="list-unstyled">
				<li><b>{{ $user->supplySet->node->full_name }}</b><br>{{ $user->supplySet->node->personnel}}명/{{$user->supplySet->node->capacity}}명 <a href="#" class="update_personnel">[수정]</a> </li>
				<li>
					<b>산하 총계</b><br> {{$personnelSum}}명/{{$capacitySum}}명
				</li>
			</ul>
			<ul>
				
			</ul>
		</small></p>
		@endif
	</div>
</div>