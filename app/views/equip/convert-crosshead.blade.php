@extends('layouts.master')


@section('content')

<div class="row">
	<div class="col-xs-12">

		<ul class="nav nav-tabs">
			<li><a href="{{url('equips/convert?is_import=true')}}">입고내역</a></li>
			<li><a href="{{url('equips/convert?is_import=false')}}">출고내역</a></li>
			<li class="active"><a href="{{url('equips/convert_cross_head')}}">청간전환</a></li>
		</ul>

		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title"><strong>지방청간 관리전환 승인</strong></h3>
			</div>
			<div class="panel-body">
				<div class="well well-sm">
					<form class="form-horizontal" id="data_table_form">
						<h5>조회조건</h5>
						<div class="row">
							<div class="col-xs-6 form-group">
								<label for="start" class="col-xs-3 control-label">
									관리전환일자
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
				
				<table class="table table-condensed table-bordered table-hover table-striped" id="data_table">
					<thead>
						<tr>
							<th>
								날짜
							</th>
							<th>
								장비명
							</th>
							<th>
								구분
							</th>
							<th>
								출처
							</th>
							<th>
								대상
							</th>
							<th>
								총 수량
							</th>
							<th>
								승인여부
							</th>
						</tr>
					</thead>
					<tbody>
					@if (count($converts) > 0) 
						@foreach ($converts as $convert)
							<tr data-id="{{$convert->id}}">
								<td>
									{{ $convert->converted_date }}
								</td>
								<td>
									<a href="{{ url('equips/convert/'.$convert->id)}}">{{ $convert->item->code->title }}</a>
								</td>
								<td>
									{{ $convert->item->classification.' / '.$convert->item->maker_name }}
								</td>
								<td>
									{{ $convert->fromNode->full_name}}
								</td>
								<td>
									{{ $convert->targetNode->full_name}}
								</td>
								<td>
									{{ number_format($convert->children->sum('count')) }}
								</td>
								<td>
									@if($convert->head_confirmed==0)
										<span class="label label-danger"><span class="glyphicon glyphicon-question-sign"></span> 미승인</span>
									@else
										<span class="label label-success"><span class="glyphicon glyphicon-ok"></span> 승인됨</span>
									@endif
								</td>
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
				{{ $converts->links() }}

			</div>
		</div>
	</div>
</div>

@stop
@section('scripts')
{{ HTML::datepicker() }}
@stop