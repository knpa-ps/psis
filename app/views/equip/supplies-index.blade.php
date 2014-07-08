@extends('layouts.master')

@section('content')

<div class="row">
	<div class="col-xs-12">
		<div class="panel-default panel">
			<div class="panel-heading">
				<h3 class="panel-title">
					<strong>보급관리</strong>
				</h3>
			</div>
			<div class="panel-body">
				<div class="well well-sm">
					<form class="form-horizontal" id="data_table_form">
						<h5>조회조건</h5>
						<div class="row">
							<div class="col-xs-6 form-group">
								<label for="start" class="col-xs-3 control-label">
									보급일자
								</label>
								<div class="col-xs-9">
									<div class="input-daterange input-group">
									    <input type="text" class="input-sm form-control" name="start" 
									    value="{{ $start }}">
									    <span class="input-group-addon">~</span>
									    <input type="text" class="input-sm form-control" name="end"
									    value="{{ $end }}" >
									</div>
								</div>
							</div>
							<div class="col-xs-6 form-group">
								<label for="item_name" class="control-label col-xs-3">
									장비명
								</label>
								<div class="col-xs-9">
									<input type="text" class="input-sm form-control" id="item_name" name="item_name">
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-xs-12">
								<div class="pull-right">
									<button class="btn btn-primary btn-xs" type="submit"><span class="glyphicon glyphicon-ok"></span> 조회</button>
									<!-- <button class="btn btn-default btn-xs" type="button"><span class="glyphicon glyphicon-download"></span> 다운로드</button> -->
								</div>
								<div class="clearfix"></div>
							</div>
						</div>

					</form>
				</div>
		
				<div class="toolbar-table">
					<a href="{{url('equips/supplies/create')}}" class="btn-xs pull-right btn btn-info"><span class="glyphicon glyphicon-plus"></span> 보급내역추가</a>
					<div class="clearfix"></div>
				</div>

				<table class="table table-condensed table-bordered table-hover table-striped" id="data_table">
					<thead>
						<tr>
							<th>
								번호
							</th>
							<th>
								장비명
							</th>
							<th>
								보급내역
							</th>
							<th>
								취득구분 (제조사/취득일)
							</th>
							<th>
								보급일자
							</th>
							<th>
								총 보급수량
							</th>
							<th>
								작업
							</th>
						</tr>
					</thead>
					<tbody>
					@if (count($data) > 0) 
						@foreach ($data as $row)
							<tr data-id="{{$row->id}}">
								<td>
									{{ $row->id }}
								</td>
								<td>
									{{ $row->item->name }}
								</td>
								<td>
									<a href="{{url('equips/supplies/'.$row->id)}}">{{ $row->title }}</a>
								</td>
								<td>
									{{ $row->inventory->model_name.' / '.$row->inventory->acq_date }}
								</td>
								<td>
									{{ $row->supply_date }}
								</td>
								<td>
									{{ number_format($row->details->sum('count')) }}
								</td>
								<td>
									<a href="{{ url('equips/supplies/'.$row->id.'/edit') }}" class="btn btn-xs btn-info btn-edit">
										<span class="glyphicon glyphicon-edit"></span> 수정
									</a>
									{{ Form::open(array(
											'url'=>url('equips/supplies/'.$row->id),
											'method'=>'delete',
											'class'=>'form-delete'
										)) }}
										<button type="submit" class="btn btn-xs btn btn-danger btn-delete">
											<span class="glyphicon glyphicon-remove"></span> 삭제
										</button>
									{{ Form::close() }}
								</td>
							</tr>
						@endforeach
					@else
						<tr>
							<td colspan="6">
								내역이 없습니다.
							</td>
						</tr>
					@endif
					</tbody>
				</table>
				{{ $data->links() }}
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
{{ HTML::datepicker() }}
<script type="text/javascript">
$(function() {
	$(".form-delete").submit(function() {
		return confirm('정말 삭제하시겠습니까?');
	});
});
</script>
@stop
