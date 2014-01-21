
@if ($breadcrumbs)
    <div id="breadcrumb-wrapper">
        <ul class="breadcrumb" id="breadcrumb">
    		@foreach ($breadcrumbs as $idx=>$menu)
    			@if ($idx == count($breadcrumbs)-1)
    				<li class="active">{{ $menu->name }}</li>	
    			@else
					<li>
						<a href="{{ action($menu->action->action) }}">{{ $menu->name }}</a>
						<span class="divider">/</span>
					</li>	
    			@endif
    			
    		@endforeach
        </ul>
    </div>
@endif