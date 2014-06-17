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
								<td><a href="#" id="{{$m->id}}" class="show-detail">{{$m->user->account_name}}</a></td>
								<td>{{$m->user->user_name}}</td>
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
						<th style="width:45%;">기존정보</th>
						<th style="width:45%;">변경요청</th>
					</tr>
					<tr>
						<th>이름</th>
						<td id="oldname"></td>
						<td id="newname"></td>
					</tr>
					<tr>
						<th>계급</th>
						<td id="oldrank"></td>
						<td id="newrank"></td>
					</tr>
					<tr>
						<th>관서</th>
						<td id="olddept"></td>
						<td id="newdept"></td>
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
$(function(){
	//현재 선택된 수정요청의 id가 저장된 변수
	var selected_id;
	//수정요청 목록에서 클릭 시 우측에 기존/변경 정보 나타나게 함
	$('.show-detail').on('click', function(){
		selected_id = this.id;
		$.ajax({
			url : base_url+"/manager/showmodified/"+this.id,
			type : 'get',
			success : function(res){
				$("#oldname").text(res.oldname);
				$("#oldrank").text(res.oldrank);
				$("#olddept").text(res.olddept);
				$("#newname").text(res.newname);
				$("#newrank").text(res.newrank);
				$("#newdept").text(res.newdept);
			}
		});
	});

	//승인 버튼 누르면 저장
	$("#approve").on('click', function(){
		$.ajax({
			url : base_url+"/manager/savemodified/"+selected_id,
			type : 'post',
			success : function(res){
				alert(res);
				location.reload();
			}
		});
	});
});
</script>
@stop