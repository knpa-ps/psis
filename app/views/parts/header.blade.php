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
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown">
            <span class="glyphicon glyphicon-user"></span> <b class="caret"></b>
          </a>
          <ul class="dropdown-menu">
            <li><a href="{{ action('UserController@displayProfile') }}"><span class="glyphicon glyphicon-exclamation-sign"></span> 내 정보</a></li>
            <li><a href="#" id="logout"><span class="glyphicon glyphicon-log-out"></span> 로그아웃</a></li>
          </ul>
        </li>
      </ul>
    </div><!--/.nav-collapse -->
  </div>
</div>
