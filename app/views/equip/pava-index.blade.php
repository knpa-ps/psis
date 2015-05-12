@extends('layouts.master')
@section('styles')
<style>
	th, td {
	  text-align: center;
	  vertical-align: middle!important;
	}
</style>
@stop
@section('content')
<div class="row">
	<div class="col-xs-12" style="overflow: auto;">
		@if ($tabId == '2')
			<ul class="nav nav-tabs">
				<li><a href="{{url('equips/pava?tab_id=1')}}">지방청별 보기</a></li>
				<li class="active"><a href="{{url('equips/pava?tab_id=2')}}">월별보기</a></li>
				<li><a href="{{url('equips/pava?tab_id=3')}}">행사별 보기</a></li>
			</ul>
			@include('equip.pava-bymonth')
		@elseif ($tabId == '3')
			<ul class="nav nav-tabs">
				<li><a href="{{url('equips/pava?tab_id=1')}}">지방청별 보기</a></li>
				<li><a href="{{url('equips/pava?tab_id=2')}}">월별보기</a></li>
				<li class="active"><a href="{{url('equips/pava?tab_id=3')}}">행사별 보기</a></li>
			</ul>
			@include('equip.pava-all')
		@else
			<ul class="nav nav-tabs">
				<li class="active"><a href="{{url('equips/pava?tab_id=1')}}">지방청별 보기</a></li>
				<li><a href="{{url('equips/pava?tab_id=2')}}">월별보기</a></li>
				<li><a href="{{url('equips/pava?tab_id=3')}}">행사별 보기</a></li>
			</ul>
			@include('equip.pava-bynode')
		@endif
		
	</div>
</div>
@stop

@section('scripts')
{{ HTML::datepicker() }}
@stop