@extends('layouts.base')

@section('body')
	<div class="col-xs-12" style="margin-top: 15px;">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">
					<b>{{ $node->node_name }} 관리전환 리스트</b>
				</h3>
			</div>
			<div class="panel-body">
				<div class="row">
					<form class="form-horizontal">
						<div class="col-xs-6 form-group">
							<label for="date" class="col-xs-4 control-label">날짜</label>
							<input type="text" class="col-xs-8 input-sm input-datepicker" name="date" id="date" value="{{ $selectedDate }}">
						</div>
						<div class="col-xs-6">
							<button id="show" class="hidden btn btn-xs btn-primary col-xs-2">조회</button>
						</div>
					</form>
				</div>
				<div class="row">
					<div class="col-xs-12">
						<table class="table table-condensed table-hover table-striped table-bordered" id="capsaicin_table">
							<thead>
								<tr>
									<th>관리전환명</th>
									<th></th>
								</tr>
							</thead>
							<tbody>
								@if(sizeof($events)==0)
								<tr>
									<td colspan="3">내역이 없습니다.</td>
								</tr>
								@else
								@foreach ($events as $e)
								<tr id="{{$e->id}}">
									<td>{{ $e->event_name }}</td>
									<td>
										<a href="#" class="delete label label-danger"><span class="glyphicon glyphicon-remove"></span> 삭제</a>
									</td>
								</tr>
								@endforeach

								@endif
							</tbody>
							<tfoot>
								{{ Form::open(array(
									'id'=>'event_form',
									'class'=>'form-horizontal'
								)) }}
								<tr>
									<td><input type="text" class="input-sm col-xs-12" id="event_name" name="event_name"></td>
									<td>
										<a href="#" class="add-usage btn btn-xs btn-success" id="add_cross_event"><span class="glyphicon glyphicon-plus"></span> 추가</a>
									</td>
								</tr>
								<input type="text" class="hidden" name="date" value="{{ $selectedDate }}">
								{{ Form::close(); }}
							</tfoot>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
@stop
@section('scripts')
{{ HTML::script('static/vendor/bootstrap-datepicker/js/bootstrap-datepicker.js') }}
{{ HTML::script('static/vendor/bootstrap-datepicker/js/locales/bootstrap-datepicker.kr.js') }}
{{ HTML::script('static/vendor/bootstrap-datepicker/js/defaults.js') }}

<script type="text/javascript">
$(function(){
	$("#date").on('change', function() {
		$("#show").trigger('click');
	})

	$("#add_cross_event").on('click', function() {
		var formData = $("#event_form").serialize();
		$.ajax({
			url : base_url+"/equips/capsaicin/admin/add_cross_event",
			data : formData,
			type : "post",
			success: function(res){
				alert(res);
				location.reload();
			}
		});
	});

	$(".delete").on('click', function() {
		confirm("삭제하시겠습니까?");
		var eventId = $(this).parent().parent().attr('id');
		$.ajax({
			url : base_url+"/equips/capsaicin/admin/delete_cross_event/"+eventId,
			type : "post",
			success: function(res){
				alert(res);
				location.reload();
			}
		});
	});
});
</script>
{{ HTML::style('static/vendor/bootstrap-datepicker/css/datepicker3.css') }}
@stop
