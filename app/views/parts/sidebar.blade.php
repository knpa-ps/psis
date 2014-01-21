<?php $GLOBALS['showSidebar'] = false; ?>
@foreach ($menus as $menu)
@if ($menu->is_active && isset($menu->children))
	<?php $GLOBALS['showSidebar'] = true; ?>
	<!-- left menu starts -->
	<div class="span2 main-menu-span" id="sidebar">
	    <div class="well nav-collapse sidebar-nav">
	        <ul class="nav nav-tabs nav-stacked main-menu">
				<li class="nav-header hidden-tablet">
					{{ $menu->name }}	
		        </li>
				@foreach ($menu->children as $c)
					@if ($c->is_active)
						<li class="active">
					@else
						<li>
					@endif
					<a href="{{ action($c->action->action) }}">
						<i class="icon-chevron-right"></i>
						<span class="hidden-tablet">{{ $c->name }}</span>
					</a>
					</li>
				@endforeach
		    </ul>
	    </div><!--/.well -->
	</div><!--/span-->
	<!-- left menu ends -->
@endif
@endforeach
