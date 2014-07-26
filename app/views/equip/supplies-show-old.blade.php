@extends('layouts.master')

@section('styles')
<style type="text/css" media="screen">
	.form-delete {
		display:inline-block;
	}
</style>
@stop

@section('content')

<div class="row">
	<div class="col-xs-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				
				<h3 class="panel-title">
				 	<a href="{{url('equips/supplies')}}"><span class="glyphicon glyphicon-chevron-left"></span></a> <strong>보급내역조회</strong>
				</h3>
			</div>
			<div class="panel-body">
				<div class="toolbar-table">
					장비명
					<strong>{{$supply->item->name}}</strong>
					<div class="pull-right">
						<a href="{{ url('equips/supplies/'.$supply->id.'/edit') }}" class="btn btn-xs btn-info btn-edit">
							<span class="glyphicon glyphicon-edit"></span> 수정
						</a>
						{{ Form::open(array(
								'url'=>url('equips/supplies/'.$supply->id),
								'method'=>'delete',
								'class'=>'form-delete'
							)) }}
							<button type="submit" class="btn btn-xs btn btn-danger btn-delete">
								<span class="glyphicon glyphicon-remove"></span> 삭제
							</button>
						{{ Form::close() }}
					</div>
					<div class="clearfix"></div>
				</div>
				<table class="table">
					<tbody>
						<tr>
							<th>보급내역</th>
							<td>{{$supply->title}}</td>
							<th>보급일자</th>
							<td>{{$supply->supply_date}}</td>
						</tr>
						<tr>
							<th>자료입력</th>
							<td>{{ $supply->creator->rank->title }} {{ $supply->creator->user_name }}</td>
							<th>자료입력일시</th>
							<td>{{$supply->created_at}}</td>
						</tr>
					</tbody>
				</table>

				<table class="table table-bordered table-hover table-striped table-condensed" id="data_table">
					<thead>
						<tr>
							<th style="width: 45%;">대상관서</th>
							<th style="width: 45%;">지급수량</th>
							<th style="width: 10%;"></th>
						</tr>
					</thead>
					<tbody>
						@foreach ($supply->details as $detail)
						<tr>
							<td>
								{{ $detail->department->full_name }}
							</td>
							<td>
								{{ number_format($detail->count) }}
							</td>
							<td>
								<a href="#" id="{{$detail->id}}" class="remove label label-danger">삭제</a>
							</td>
						</tr>
						@endforeach
					</tbody>
					<tfoot>
						<tr>
							<td style="background-color: yellow;">
								<strong>총 지급수량</strong>
							</td>
							<td colspan="2" style="background-color: yellow;">
								<b id="sum_count">&nbsp{{ number_format($supply->details->sum('count')) }}</b>
							</td>
						</tr>
					</tfoot>
				</table>
				<br>
				{{ Form::open(array(
					'url' => 'equips/supplies/'.$supply->id,
					'method' => 'put',
					'role' => 'form',
					'class' => 'form-horizontal',
					'id' => 'supply_form'
				)) }}
					<fieldset>
						<b>보급내역 추가</b>
						<hr>
						<div class="row">
							<div class="col-xs-4">
								<div class="form-group">
									<label for="dept_id" class="col-xs-4 control-label">대상부서</label>
									<div class="col-xs-8">
										{{ View::make('widget.dept-selector', array('id'=>'dept_id')) }}
									</div>
								</div>
							</div>
							<div class="col-xs-4">
								<div class="form-group">
									<label for="count" class="col-xs-4 control-label">보급수량</label>
									<div class="col-xs-8">
										<input type="text" id="count" name="count">
									</div>
								</div>
							</div>
							<div class="col-xs-4">
								<div class="form-group">
									<div class="col-xs-4 col-xs-offset-2">
										<button type="button" class="btn btn-success" id="add_supply">
											<span class="glyphicon glyphicon-plus"> <b>추가</b></span>
										</button>
									</div>
								</div>
							</div>
						</div>
					</fieldset>
				{{ Form::close() }}
			</div>
		</div>
	</div>
</div>

@stop


@section('scripts')
{{ HTML::script('static/vendor/validate/jquery.validate.min.js') }}
{{ HTML::script('static/vendor/validate/additional-methods.min.js') }}
{{ HTML::script('static/vendor/validate/messages_ko.js') }}
<script type="text/javascript">

$(function() {
	$("#data_table").on('click','.remove', function(){
		var this_id = this.id
		$.ajax({
			url: base_url+'/equips/supplies/'+{{ $supply->id }}+'/detail/'+this_id,
			type: 'delete',
			success: function(res){
				$('#' + this_id).parent().parent().remove();

				$('#sum_count').html(res.sum);
			}
		});
	})
	$("#supply_form").validate({
		rules : {
			dept_id_display: {
				required: true
			},
			count: {
				required: true,
				number: true
			}
		}
	});

	$("#add_supply").on('click', function(){
		if($("#supply_form").valid()){
			var formData = $('#supply_form').serializeArray();
			$.ajax({
				url : base_url+'/equips/supplies/'+{{ $supply->id }}+'/detail',
				type : 'put',
				data : formData,
				success : function(res){
					$("#count").val('');
					$("#dept_id_display").val('');
					$('#data_table tbody').append(res.row);

					$('#sum_count').html(res.sum);
				}
			});
		} else {
			alert("부서와 보급수량을 입력하세요.");
		}
	});

	$(".form-delete").submit(function() {
		return confirm('정말 삭제하시겠습니까?');
	});
});
</script>
@stop
