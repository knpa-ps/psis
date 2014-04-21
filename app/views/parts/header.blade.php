<div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
  <div class="container">
    
    <div class="navbar-header">
      <a class="navbar-brand" href="{{ url('/') }}"> @lang('global.t_portal') </a>
    </div>
    
    <div class="navbar-collapse collapse">
      <ul class="nav navbar-nav">
      @foreach ($menus['navbar'] as $menu)

        <li class="{{ $menu->is_active ? 'active' : '' }}">
          <a href="{{ url($menu->url) }}">{{ $menu->name }}</a>
        </li>

      @endforeach
      </ul>
      <ul class="nav navbar-nav navbar-right">

      </ul>
    </div><!--/.nav-collapse -->
  </div>
</div>
