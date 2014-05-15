@extends('layouts.master')

@section('content')
<div class="row">
	<div class="col-xs-6">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title pull-left"><b>정보수정 요청 목록</b></h3>
				<div class="clearfix"></div>
			</div>
			<div class="panel-body">
				<table class="table table-condensed table-striped table-hover table-bordered" id="users_to_modify">
					<thead>
						<tr>
							<th>
								계정
							</th>
							<th>
								이름
							</th>
							<th>
								요청일시
							</th>
						</tr>
					</thead>
					<tbody>
						@foreach ($mods as $m)
							<tr>
								<td>{{$m->user->account_name}}</td>
								<td><a href="#" id="{{$m->id}}" class="show-detail">{{$m->user->user_name}}</a></td>
								<td>{{$m->created_at}}</td>
							</tr>	
						@endforeach
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<div class="col-xs-6">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title pull-left"><b>정보수정 요청 내역</b></h3>
				<div class="clearfix"></div>
			</div>
			<div class="panel-body">
				<table class="table table-striped" id="requested_content">
					<tr>
						<th></th>
						<th>기존정보</th>
						<th>변경요청</th>
					</tr>
					<tr>
						<th>이름</th>
						<td>기존이름</td>
						<td>새이름</td>
					</tr>
					<tr>
						<th>계급</th>
						<td>기존계급</td>
						<td>계애급</td>
					</tr>
					<tr>
						<th>관서</th>
						<td>기존관서</td>
						<td>과안서</td>
					</tr>
				</table>
				
				<button class="btn btn-primary btn-xs pull-right" id="approve">
					<span class="glyphicon glyphicon-ok-sign"> 승인</span>
				</button>
			</div>
		</div>
	</div>
</div>
@stop
@section('scripts')
<script type="text/javascript">
	
</script>
@stop