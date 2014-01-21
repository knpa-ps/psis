<div class="navbar navbar-inverse navbar-default navbar-fixed-top" role="navigation">
    <div class="container">
        <div class="navbar-header">
            <a class="navbar-brand" href="{{ action('HomeController@showDashboard') }}">@lang('strings.site_logo_text')</a>
        </div>
        <div class="navbar-collapse collapse">
            <ul class="nav navbar-nav">
            @foreach ($menus as $menu)
                @if ($menu->is_active)
                    <li class="dropdown active">
                @else
                    <li class="dropdown">
                @endif

                    @if (isset($menu->children) && !empty($menu->children))
                        <a href="{{ action($menu->action->action) }}" 
                        class="dropdown-toggle" 
                        data-toggle="dropdown">
                    @else
                        <a href="{{ action($menu->action->action) }}">
                    @endif

                    {{ $menu->name }} 

                    @if (isset($menu->children) && !empty($menu->children))
                    <b class="caret"></b></a>
                    <ul class="dropdown-menu">
                        @foreach ($menu->children as $c)
                            @if ($c->is_active)
                                <li class="active">
                            @else
                                <li>
                            @endif
                                <a href="{{ action($c->action->action) }}">{{ $c->name }}</a>
                            </li>    
                        @endforeach
                    </ul>
                    @else
                    </a>
                    @endif
                </li>
            @endforeach
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li>
                    <a href="#" role="button" class="dropdown-toggle" data-toggle="dropdown">
                    <span class="glyphicon glyphicon-user white"></span>
                        {{ $user->user_name }}
                        <i class="caret"></i>
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <a tabindex="-1" href="{{ action('HomeController@showProfile') }}">
                                @lang('strings.my_info')
                            </a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a tabindex="-1"  
                            onclick="javascript:if (confirm('{{ Lang::get('strings.logout_confirm') }}')) { window.location='{{ action('AuthController@doLogout') }}'}">
                            @lang('strings.logout')
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div><!--/.nav-collapse -->
    </div>
</div>