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
		@if ($tabDept == 'true')
			<ul class="nav nav-tabs">
				<li><a href="{{url('equips/capsaicin?tab_dept=false')}}">전체보기</a></li>
				<li class="active"><a href="{{url('equips/capsaicin?tab_dept=true')}}">지방청별 보기</a></li>
			</ul>
			@include('equip.capsaicin-bynode')
		@else
			<ul class="nav nav-tabs">
				<li class="active"><a href="{{url('equips/capsaicin?tab_dept=false')}}">전체보기</a></li>
				<li><a href="{{url('equips/capsaicin?tab_dept=true')}}">지방청별 보기</a></li>
			</ul>
			@include('equip.capsaicin-all')
		@endif
		
	</div>
</div>
@stop

@section('scripts')
{{ HTML::datepicker() }}
@stop