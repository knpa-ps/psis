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
								</div>
								<div class="clearfix"></div>
							</div>
						</div>

					</form>
				</div>

				<div class="toolbar-table">
					<form action="{{url('equips/supplies/create')}}">
						<label style="margin-top: 9px; text-align: center;" for="item_to_supply" class="control-label col-xs-1">장비선택</label>
						<div class="col-xs-9">
							<select name="item" id="item_to_supply" class="form-control">
								@if(count($items)>0)
									@foreach($items as $i)
										<option value="{{$i->id}}">{{substr($i->acquired_date, 0, 4).' '.$i->code->title.' ('.$i->maker_name.', '.$i->classification.')'}}</option>
									@endforeach
								@else
									<option value="0">올해 취득한 장비가 없습니다.</option>
								@endif

							</select>
						</div>
						<button type="submit" style="margin-top: 3px;" class="col-xs-2 btn-xs pull-right btn btn-info"><span class="glyphicon glyphicon-plus"></span> 보급하기</button>
						<div class="clearfix"></div>
					</form>
				</div>

				<table class="table table-condensed table-bordered table-hover table-striped" id="data_table">
					<thead>
						<tr>
							<th width="10%">
								보급일자
							</th>
							<th>
								장비명
							</th>
							<th>
								납품일
							</th>
							<th>
								출고관서
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
					@if (sizeof($supplies) > 0)
						@foreach ($supplies as $supply)
							<tr data-id="{{$supply->id}}">
								<td>
									{{ $supply->supplied_date }}
								</td>
								<td>
									<a href="{{ url('equips/supplies/'.$supply->id)}}">{{ $supply->item->code->title.' ('.$supply->item->maker_name.', '.$supply->item->classification.')'}}</a>
								</td>
								<td>
									{{ $supply->item->acquired_date }}
								</td>
								<td>
									{{ $supply->node->full_name }}
								</td>
								<td>
									{{ number_format($supply->children->sum('count')) }}
								</td>
								@if($supply->from_node_id == $user->supplyNode->id)
								<td>
									{{ Form::open(array(
											'url'=>url('equips/supplies/'.$supply->id),
											'method'=>'delete',
											'class'=>'form-delete'
										)) }}
										<button type="submit" class="btn btn-xs btn btn-danger btn-delete">
											<span class="glyphicon glyphicon-remove"></span> 보급취소
										</button>
									{{ Form::close() }}
								</td>
								@else
								<td>
								</td>
								@endif
							</tr>
						@endforeach
					@else
						<tr>
							<td colspan="7">
								내역이 없습니다.
							</td>
						</tr>
					@endif
					</tbody>
				</table>
				{{ $supplies->links() }}
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
