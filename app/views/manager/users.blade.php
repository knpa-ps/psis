@extends('layouts.master')

@section('content')

<div class="row">
	<div class="col-xs-12">
		<div class="well well-small">
			<form class="form-horizontal" id="data_table_form" action="{{action('UserController@index')}}">

				<div class="row">
					<div class="row col-xs-12">
						<div class="form-group col-xs-6">
							<label for="sit_code" class="control-label col-xs-4">
								사용자 그룹
							</label>
							<div class="col-xs-8">
								{{ Form::select('group', $manageGroups, null, array('class' => 'form-control input-sm', 'id' => 'user_group') )}}
							</div>
						</div>
						<div class="form-group col-xs-6">
							<label for="use_code" class="control-label col-xs-4">
								활성화 여부
							</label>
							<div class="col-xs-8">
								{{ Form::select('active', array('1'=>'활성화', '0'=>'비활성화','all' => '전체'), 'all' , array('class'=>'form-control input-sm', 'id'=>'active')  ) }}
							</div>
						</div>
					</div>
					<div class="row col-xs-12">
						<div class="form-group col-xs-6">
							<label for="account_name" class="control-label col-xs-4">
								계정/이름
							</label>
							<div class="col-xs-8">
								<input type="text" class="input-sm form-control" id="account_name" name="account_name">
							</div>
						</div>
						<div class="form-group col-xs-6">
							<label for="use_code" class="control-label col-xs-4">
								정보 수정 신청자
							</label>
							<div class="col-xs-8">
								{{ Form::select('modify_requested', array('1'=>'신청자', 'all' => '전체'), 'all' , array('class'=>'form-control input-sm', 'id'=>'modify_requested')  ) }}
							</div>					
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-xs-12">
						<div class="pull-right">
							<button class="btn btn-primary btn-xs" type="submit"><span class="glyphicon glyphicon-ok"></span> 조회</button>
						</div>
						<div class="clearfix"></div>
					</div>
				</div>

			</form>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-xs-12">
		<div class="panel panel-default">
			<div class="panel-body">
				<a href="{{ url('manager/users/create') }}">create</a>
				<table class="table table-condensed table-bordered table-hover table-striped" id="users_table">
					<thead>
						<tr>
							<th>
								id
							</th>
							<th>계정</th>
							<th>이름</th>
							<th>rank</th>
							<th>dept</th>
							<th>status</th>

						</tr>
					</thead>
					<tbody>
						@foreach ($users as $u)
						<tr>
							<td>{{ $u->id }}</td>
							<td><a href="{{ url('manager/users/'.$u->id) }}">{{ $u->account_name }}</a></td>
							<td>{{ $u->user_name }}</td>
							<td>{{ $u->rank->title }}</td>
							<td>{{ $u->department->full_name }}</td>
							<td>{{ $u->activated?'active':'inactive' }}</td>
						</tr>
						@endforeach
					</tbody>
				</table>

				{{ $users->links() }}
			</div>
		</div>
	</div>
</div>

@stop
