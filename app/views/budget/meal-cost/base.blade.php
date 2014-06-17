@extends('layouts.master')

@section('content')

<?php $type = isset($type) ? $type : '' ?>

<div class="row">
	<div class="col-xs-12">
		<ul class="nav nav-tabs pull-left">
			<li class="active"><a href="#"><strong>동원급식비</strong></a></li>
			<li {{ $type=='raw'?'class="active"':'' }}><a href="{{ url('budgets/meal-cost') }}?type=raw">원본</a></li>
			<!-- <li {{ $type=='stat-sit'?'class="active"':'' }}><a href="{{ url('budgets/meal-cost') }}?type=stat-sit" >동원상황별통계</a></li> -->
		</ul>
		<a href="{{ url('budgets/meal-cost/create') }}" class="btn btn-info pull-right">
			<span class="glyphicon glyphicon-plus"></span> 자료입력
		</a>
		<div class="clearfix"></div>
		<div class="tab-content">
			<div class="tab-pane active in">
				<div class="panel panel-default">
					<div class="panel-body">
						@yield('tab-content')
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

@stop