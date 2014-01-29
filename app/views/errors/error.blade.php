@extends('layouts.master')

@section('content')
<div class="row-fluid">
	<div class="span12">

	<div class="hero-unit">
        <h1>{{ $header or "오류가 발생했습니다." }}</h1>
        <br>
			<p class="lead">{{ $message or "해당 요청에 대해 권한이 없거나 잘못된 경로로 접근했을 수 있습니다. 계속해서 문제가 반복될 시에는 관리자에게 문의해주세요." }}</p>
      </div>
	</div>
</div>
@stop