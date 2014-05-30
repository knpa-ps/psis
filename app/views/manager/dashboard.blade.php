@extends('layouts.master')

@section('content')


<div class="row">
	<div class="col-xs-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title pull-left">
					<b>계정 정보 수정 요청 목록</b>
				</h3>
				<a href="{{URL::to('manager/modify')}}" class="label label-primary pull-right">더보기</a>
				<div class="clearfix"></div>
			</div>
			<div class="panel-body">
				<table class="table table-condensed table-striped table-hover table-bordered" id="user_mods_table">
					<thead>
						<tr>
							<th>
								계정
							</th>
							<th>
								이름
							</th>
							<th>
								계급
							</th>
							<th>
								부서
							</th>
							<th>
								정보수정 요청시간
							</th>
						</tr>
					</thead>
					<tbody>
						@foreach($mods as $m)
							<tr>
								<td><a href="{{URL::to('manager/modify')}}">{{$m->user->email}}</a></td>
								<td>{{$m->user->user_name}}</td>
								<td>{{ $m->user->rank->title }}</td>
								<td>{{ $m->user->department->full_name}}</td>
								<td>{{ $m->created_at }}</td>
							</tr>
						@endforeach
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>



@stop