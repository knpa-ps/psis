<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<title>
    @lang('strings.site_title') :: {{ $title }}  
</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<!-- The styles -->

<link rel="stylesheet" href="{{ asset('static/css/bootstrap-classic.css') }}" id="bs-css" />
<style type="text/css">
  .sidebar-nav {
    padding: 9px 0;
  }
</style>
{{ HTML::style('static/css/bootstrap-responsive.css') }}
{{ HTML::script('static/js/respond.min.js') }}
{{ HTML::style('static/css/charisma-app.css') }}
{{ HTML::style('static/css/jquery-ui-1.8.21.custom.css') }}
{{ HTML::style('static/css/chosen.css') }}
{{ HTML::style('static/css/uniform.default.css') }}
{{ HTML::style('static/css/jquery.noty.css') }}
{{ HTML::style('static/css/noty_theme_default.css') }}
{{ HTML::style('static/css/jquery.iphone.toggle.css') }}
{{ HTML::style('static/css/opa-icons.css') }}
{{ HTML::style('static/css/uploadify.css') }}
{{ HTML::style('static/css/psis/common.css') }}
<!-- The HTML5 shim, for IE6-8 support of HTML5 elements -->

<!--[if lt IE 9]>
    {{ HTML::script('static/js/html5shiv.js') }}
<![endif]-->

<!-- The fav icon -->
<link rel="shortcut icon" href="{{ asset('static/img/favicon.ico') }}">
