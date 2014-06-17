@extends('layouts.master')

@section('content')
<div class="col-xs-12">
	<h1 class="page-header">권한 관리 <small>{{$user->account_name or ''}}</small></h1>
		<div class="row">
			<div class="col-xs-6">
				<div class="panel panel-default">
					<div class="panel-heading">
						<h3 class="panel-title pull-left">
							<strong>그룹</strong>
						</h3>
						<div class="clearfix"></div>
					</div>
					<div class="panel-body">
						<div class="list-group" id="group_list">
							@foreach($groups as $group)
								<a id="{{ $group->id }}" href="#" class="list-group-item">{{ $group->name }}</a>
							@endforeach
						</div>
					</div>
				</div>
			</div>
			<div class="col-xs-6">
				<div class="panel panel-default">
					<div class="panel-heading">
						<h3 class="panel-title pull-left">
							<strong>권한</strong>
						</h3>
						<div class="clearfix"></div>
					</div>
					<div class="panel-body" id="permissions_container">
						<p>권한을 바꿀 그룹을 선택하세요.</p>
					</div>
				</div>
			</div>
		</div>
</div>
@stop
@section('scripts')
<script type="text/javascript">
	$(function(){
		//그룹 선택시 해당 그룹 권한들 나오게 하는거
		$('#group_list>a').click(function(){
			$('#group_list>a').removeClass("active");
			$(this).addClass("active");
			var params = {};
			params['id'] = this.id;
			$.ajax({
				url : base_url+"/admin/permission/get",
				type : 'post',
				data : params,
				success : function(response){
					$('#permissions_container').html(response);
				}
			});
		});
	});
</script>
@stop