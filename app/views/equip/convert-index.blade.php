@extends('layouts.master')


@section('content')

<div class="row">
	<div class="col-xs-12">

		<ul class="nav nav-tabs">
			@if ($isImport == true)
				<li class="active"><a href="{{url('equips/convert?is_import=true')}}">입고내역</a></li>
				<li><a href="{{url('equips/convert?is_import=false')}}">출고내역</a></li>
			@else
				<li><a href="{{url('equips/convert?is_import=true')}}">입고내역</a></li>
				<li class="active"><a href="{{url('equips/convert?is_import=false')}}">출고내역</a></li>
			@endif
			@if ($user->supplyNode->type_code == 'D001')
				<li><a href="{{url('equips/convert_cross_head')}}">청간전환</a></li>
			@endif
		</ul>

		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title"><strong>관리전환</strong></h3>
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
								<input type="text" class="hidden" name="is_import" value="{{ $isImport==true ? 'true' : 'false'  }}">
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
				
				@if ($isImport!=true)
				<div class="toolbar-table">
					<form action="{{url('equips/convert/create')}}">
						<label style="margin-top: 9px; text-align: center;" for="item_to_convert" class="control-label col-xs-1">장비선택</label>
						<div class="col-xs-9">
							<select name="item" id="item_to_convert" class="form-control">
								@if(count($items)>0)
									@foreach($items as $i)
										<option value="{{$i->id}}">{{substr($i->acquired_date, 0, 4).' '.$i->code->title}} ({{$i->maker_name.' '.$i->classification}})</option>
									@endforeach
								@else
									<option value="0">보유중인 장비가 없습니다.</option>
								@endif
								
							</select>
						</div>
						<button type="submit" style="margin-top: 3px;" class="col-xs-2 btn-xs pull-right btn btn-info"><span class="glyphicon glyphicon-plus"></span> 관리전환하기</button>
						<div class="clearfix"></div>
					</form>
				</div>
				@endif
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
							@if ($isImport != true)
								<th>
									대상관서
								</th>
							@else
								<th>
									출처
								</th>
							@endif
							<th>
								총 수량
							</th>
							<th>
								확인여부
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
									<a href="{{ url('equips/convert/'.$convert->id)}}">{{ substr($convert->item->acquired_date, 0, 4).' '.$convert->item->code->title }}</a>
								</td>
								<td>
									{{ $convert->item->classification.' / '.$convert->item->maker_name }}
								</td>
								@if ($isImport!=true)
									<td>
										{{ explode(' ', $convert->targetNode->full_name)[0] }}
									</td>
								@else
									<td>
										{{ explode(' ', $convert->fromNode->full_name)[0] }}
									</td>
								@endif
								<td>
									{{ number_format($convert->children->sum('count')) }}
								</td>
								<td>
									@if($convert->cross_head == 1 && $convert->head_confirmed == 0)
										<span class="label label-warning"><span class="glyphicon glyphicon-question-sign"></span> 본청승인대기</span>
									@else
										@if($convert->is_confirmed==0)
											<span class="label label-danger"><span class="glyphicon glyphicon-question-sign"></span> 미확인</span>
										@else
											<span class="label label-success"><span class="glyphicon glyphicon-ok"></span> {{ $convert->confirmed_date }}</span>
										@endif
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

