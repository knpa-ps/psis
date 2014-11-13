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

			@if ($isState == 'true')
				<ul class="nav nav-tabs">
					<li class="active"><a href="{{url('equips/capsaicin/node/'.$node->id.'?is_state=true')}}">월별보기</a></li>
					<li><a href="{{url('equips/capsaicin/node/'.$node->id.'?is_state=false')}}">사용내역</a></li>
				</ul>

				@include('equip.capsaicin-node-holding')

			@else
				<ul class="nav nav-tabs">
					<li><a href="{{url('equips/capsaicin/node/'.$node->id.'?is_state=true')}}">월별보기</a></li>
					<li class="active"><a href="{{url('equips/capsaicin/node/'.$node->id.'?is_state=false')}}">사용내역</a></li>
				</ul>
				
				@include('equip.capsaicin-node-usage')

			@endif
	</div>
</div>
@stop

@section('scripts')
{{ HTML::datepicker() }}
@stop