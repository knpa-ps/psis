<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<title>
    @if (empty($title))
        @lang('strings.site_title')
    @else
        {{ $title }} :: @lang('strings.site_title')
    @endif
</title>

{{ HTML::style('assets/css/bootstrap.min.css') }}
{{ HTML::style('assets/css/common.css') }}
{{ HTML::script('assets/js/vendor/respond.min.js') }}

<!--[if lt IE 9]>
{{ HTML::script('assets/js/vendor/html5shiv.js') }}
<![endif]-->