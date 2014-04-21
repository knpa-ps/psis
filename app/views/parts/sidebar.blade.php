@include('widget.sidebar-profile')

@foreach ($menus['sidebar'] as $menu)

@if ($menu->is_active)
	<h4><span class="glyphicon glyphicon-list"></span> <a href="{{ url($menu->url) }}" class="black"> {{ $menu->name }}</a></h4>
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
