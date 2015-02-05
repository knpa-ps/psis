@include('widget.sidebar-profile')

@foreach ($menus['sidebar'] as $menu)

@if ($menu->is_active)
	<h4><a style="font-size: 17px;" href="{{ url($menu->url) }}" class="black"><b>{{ $menu->name }}</b></a></h4>
	@if (isset($menu->children))
		<ul class="nav nav-pills nav-stacked">
			@foreach ($menu->children as $child)
				<li class="{{ $child->is_active ? 'active': '' }}">
					<a href="{{ url($child->url) }}">
						{{ $child->name }} 
						<span class="glyphicon glyphicon-chevron-right pull-right black"></span>
					</a>
				</li>
			@endforeach
		</ul>
	@endif
@endif

@endforeach
