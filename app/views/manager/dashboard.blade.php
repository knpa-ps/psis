@extends('layouts.master')

@section('content')


<div class="row">
	<div class="col-xs-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title pull-left">
					<b>계정 정보 수정 요청 목록</b>
				</h3>
				<span class="label pull-right label-default">more</span>
				<div class="clearfix"></div>
			</div>
			<div class="panel-body">
				<table class="table table-condensed table-striped table-hover table-bordered" id="user_mods_table">
					<thead>
						<tr>
							<th>
								name
							</th>
							<th>
								created_at
							</th>
							<th>
								status
							</th>
							<th>
								action
							</th>
						</tr>
					</thead>
					<tbody>
						@foreach($mods as $m)
							<tr>
								<td>{{$m->user->user_name}}</td>
								<td>{{ $m->created_at->format('Y.m.d H:i') }}</td>
								<td>{{ $m->approved }}</td>
								<td><a href="#">자세히</a></td>
							</tr>
						@endforeach
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>



@stop