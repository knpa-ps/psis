<!-- Sidebar -->
<div id="sidebar-wrapper">
	<div class="row">
		<div class="col-xs-12">
			<div class="panel panel-default" id="sb-profile">
				<div class="panel-heading">
					@lang('strings.my_info')
					<div class="pull-right">
						<a href="{{ action('HomeController@showProfile') }}">
							<span class="glyphicon glyphicon-edit">
							</span>
						</a>
                        <a href="#" 
                            onclick="javascript:if (confirm('{{ Lang::get('strings.logout_confirm') }}')) { window.location='{{ action('AuthController@doLogout') }}'}">
							<span class="glyphicon glyphicon-remove"></span>
						</a>
					</div>
				</div>				
				<div class="panel-body">
					<dl class="dl-horizontal">
						<dt>@lang('strings.user_name')</dt>
						<dd>{{ $user->user_name }}</dd>
						<dt>@lang('strings.department')</dt>
						<dd>{{ $dept->parseFullName().'<br>'.$user->dept_detail }}</dd>
						<dt>@lang('strings.groups')</dt>
						<dd>
						@foreach ($user->getGroups() as $group)
							{{ $group->name }}
						@endforeach
						</dd>
					</dl>
				</div>
			</div>
		</div>
	</div>
    <ul class="sidebar-nav" id="sidebar">
    @foreach ($menus as $menu)
	@if ($menu->is_active && isset($menu->children))
		<li class="sidebar-brand">
			<a href="{{ action($menu->action->action) }}">
				{{ $menu->name }}
			</a>
        </li>
		@foreach ($menu->children as $c)
			@if ($c->is_active)
				<li class="active">
			@else
				<li>
			@endif
			<a href="{{ action($c->action->action) }}">
				{{ $c->name }}
			</a>
			</li>
		@endforeach
	@endif
    @endforeach
    </ul>
</div>