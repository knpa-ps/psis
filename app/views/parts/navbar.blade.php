<!-- topbar starts -->
<div class="navbar">
    <div class="navbar-inner">
        <div class="container-fluid">
            <a class="btn btn-navbar" data-toggle="collapse" data-target=".top-nav.nav-collapse,.sidebar-nav.nav-collapse">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </a>
            <a class="brand" href="{{ action('HomeController@showDashboard') }}">
                <span>@lang('strings.site_logo_text')</span>
            </a>
            
            <!-- theme selector starts -->
            <div class="btn-group pull-right theme-container" >
                <a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
                    <i class="icon-tint"></i><span class="hidden-phone"> @lang('strings.change_theme')</span>
                    <span class="caret"></span>
                </a>
                <ul class="dropdown-menu" id="themes">
                    <li><a data-value="classic" href="#"><i class="icon-blank"></i> Classic</a></li>
                    <li><a data-value="cerulean" href="#"><i class="icon-blank"></i> Cerulean</a></li>
                    <li><a data-value="redy" href="#"><i class="icon-blank"></i> Redy</a></li>
                    <li><a data-value="simplex" href="#"><i class="icon-blank"></i> Simplex</a></li>
                    <li><a data-value="slate" href="#"><i class="icon-blank"></i> Slate</a></li>
                    <li><a data-value="spacelab" href="#"><i class="icon-blank"></i> Spacelab</a></li>
                    <li><a data-value="united" href="#"><i class="icon-blank"></i> United</a></li>
                </ul>
            </div>
            <!-- theme selector ends -->
            
            <!-- user dropdown starts -->
            <div class="btn-group pull-right" >
                <a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
                    <i class="icon-user"></i><span class="hidden-phone"> {{ $user->user_name }}</span>
                    <span class="caret"></span>
                </a>
                <ul class="dropdown-menu">
                    <li><a href="{{ action('HomeController@showProfile') }}">@lang('strings.my_info')</a></li>
                    <li class="divider"></li>
                    <li>
                        <a tabindex="-1"  
                        onclick="javascript:if (confirm('{{ Lang::get('strings.logout_confirm') }}')) { window.location='{{ action('AuthController@doLogout') }}'}">
                        @lang('strings.logout')
                        </a>
                    </li>
                </ul>
            </div>
            <!-- user dropdown ends -->
            <div class="top-nav nav-collapse">
                <ul class="nav">
                @foreach ($menus as $menu)
                    @if ($menu->is_active)
                        <li class="dropdown active">
                    @else
                        <li class="dropdown">
                    @endif

                        @if (isset($menu->children) && !empty($menu->children))
                            <a href="{{ $menu->href }}" 
                            class="dropdown-toggle" 
                            data-toggle="dropdown">
                        @else
                            <a href="{{ $menu->href }}">
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
                                        <a href="{{ $c->href }}">{{ $c->name }}</a>
                                    </li>    
                                @endforeach
                            </ul>
                        @else
                            </a>
                        @endif
                        
                    </li>
                @endforeach

                </ul>
            </div><!--/.nav-collapse -->
        </div>
    </div>
</div>
<!-- topbar ends -->
