@extends('layouts.master')

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
							<th>대상관서</th>
							<th>지급수량</th>
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
						</tr>
						@endforeach
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>

@stop
@section('styles')
<style type="text/css" media="screen">
	.form-delete {
		display:inline-block;
	}
</style>
@stop

@section('scripts')
<script type="text/javascript">
$(function() {
	$(".form-delete").submit(function() {
		return confirm('정말 삭제하시겠습니까?');
	});
});
</script>
@stop
