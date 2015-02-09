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
								{{ Form::select('group', $manageGroups, $group, array('class' => 'form-control input-sm', 'id' => 'user_group') )}}
							</div>
						</div>
						<div class="form-group col-xs-6">
							<label for="use_code" class="control-label col-xs-4">
								활성화 여부
							</label>
							<div class="col-xs-8">
								{{ Form::select('active', array('1'=>'활성화', '0'=>'비활성화','all' => '전체'), $active , array('class'=>'form-control input-sm', 'id'=>'active')  ) }}
							</div>
						</div>
					</div>
					<div class="row col-xs-12">
						<div class="form-group col-xs-6">
							<label for="account_name" class="control-label col-xs-4">
								계정/이름
							</label>
							<div class="col-xs-8">
								<input type="text" class="input-sm form-control" id="account_name" name="account_name" value="{{$accountName}}">
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
			<div class="panel-heading">
				<h3 class="panel-title">
					<strong>사용자 목록</strong>
				</h3>
			</div>
			<div class="panel-body">
				<div class="btn-toolbar toolbar-table" role="toolbar">
					<a href="{{ url('manager/users/create') }}" class="btn btn-primary pull-right btn-xs">사용자 추가 <span class="glyphicon glyphicon-plus"></span></a>
				</div>
				<table class="table table-condensed table-bordered table-hover table-striped" id="users_table">
					<thead>
						<tr>
							<th>
								id
							</th>
							<th>계정</th>
							<th>이름</th>
							<th>계급</th>
							<th>관서</th>
							<th>계정 상태</th>

						</tr>
					</thead>
					<tbody>
						@foreach ($users as $u)
						<tr>
							<td>{{ $u->id }}</td>
							<td><a href="{{ url('manager/users/'.$u->id.'/edit') }}">{{ $u->account_name }}</a></td>
							<td>{{ $u->user_name }}</td>
							<td>{{ $u->rank->title }}</td>
							<td>{{ $u->department->full_name }}</td>
							<td>{{ $u->activated?'활성화':'비활성화' }}</td>
						</tr>
						@endforeach
					</tbody>
				</table>
				{{ $users->appends(array('group'=>$group, 'active'=>$active, 'account_name'=>$accountName ))->links() }}
			</div>
		</div>
	</div>
</div>

@stop
