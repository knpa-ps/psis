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
			@if ($user->supplySet->node->type_code == 'D001')
				<li><a href="{{url('equips/convert_cross_head')}}">청간전환</a></li>
			@endif
		</ul>

		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title"><strong>{{end($userNodeName) }} 산하 관리전환내역</strong></h3>
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
									<input type="text" class="input-sm form-control" id="item_name" name="item_name" value = "{{$itemName}}">
								</div>
								<input type="text" class="hidden" name="is_import" value="{{ $isImport==true ? 'true' : 'false'  }}">
							</div>
						</div>
						<div class="row">
							<div class="col-xs-6 form-group">
								<label for="dept_name" class="control-label col-xs-3">
									관서명
								</label>
								<div class="col-xs-9">
									<input type="text" class="input-sm form-control" id="dept_name" name="dept_name" value = "{{$deptName}}">
								</div>
							</div>
							<div class="col-xs-6 form-group">
								<label for="dept_name" class="control-label col-xs-3">
									확인여부
								</label>
								<div class="col-xs-9">
									<select class="form-control" name="checked" id="checked">
										<option value="both">전체</option>
										<option value="unchecked">미확인</option>
										<option value="checked">확인</option>
										<option value="waiting">본청승인대기</option>
									</select>
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

				@if ($isImport!=true)
				<div class="toolbar-table">
					<form action="{{url('equips/convert/create')}}">
						<label style="margin-top: 9px; text-align: center;" for="item_to_convert" class="control-label col-xs-1">장비선택</label>
						<div class="col-xs-9">
							<select name="item" id="item_to_convert" class="form-control">
								@if(count($items)>0)
								@foreach ($categories as $category)
								<optgroup label="{{$category->sort_order.'. '.$category->name}}">
									@foreach ($category->codes as $c)
									<optgroup label="{{$c->title}}">
										@foreach($items[$c->id] as $item)
											<option value="{{$item->id}}">{{substr($item->acquired_date, 0, 4).' '.$item->code->title.', '.$item->maker_name.', '.$item->classification}}</option>
										@endforeach
									@endforeach
									</optgroup>
								@endforeach
								</optgroup>
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
							<th width="10%">
								날짜
							</th>
							<th>
								장비명
							</th>
							<th>
								업체명
							</th>
							<th>
								출고관서
							</th>
							<th>
								입고관서
							</th>
							<th>
								총 수량
							</th>
							<th>
								확인여부
							</th>
							<th>
								작업
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
									<a href="{{ url('equips/convert/'.$convert->id)}}">{{ substr($convert->acquired_date, 0, 4).' '.$convert->title.' '.$convert->classification }}</a>
								</td>
								<td>
									{{ $convert->maker_name }}
								</td>
								<td>
									{{ $convert->from_node_name }}
								</td>
								<td>
									{{ $convert->target_node_name }}
								</td>
								<td>
									<!-- if 총수량 is bigger than 1000 -->
									<!-- number_format으로 소수점 1째 자리까지 나타냄 -->
									<!-- 2016.08.22 edited -->
									@if( $convert->count_sum >= 1000 )
									{{ number_format($convert->count_sum, 1) }}
									@else
									{{ $convert->count_sum }}
									@endif
								</td>
								<td>
									@if($convert->cross_head == 1 && $convert->head_confirmed == 0)
										<span class="label label-warning"><span class="glyphicon glyphicon-question-sign"></span> 본청승인대기</span>
									@else
										@if($convert->is_confirmed==0)
											<span class="label label-danger"><span class="glyphicon glyphicon-xs glyphicon-question-sign"></span> 입고관서 미확인</span>
										@else
											<span class="label label-success"><span class="glyphicon glyphicon-ok"></span> {{ $convert->confirmed_date }}</span>
										@endif
									@endif
								</td>
								<!-- 자신만 관리전환 취소버튼을 볼 수 있고, confirm되면 취소시킬 수 없음. -->
								@if(($convert->from_node_id == $user->supplySet->node->id) && ($convert->is_confirmed == 0))
									<td>
										{{ Form::open(array(
												'url'=>url('equips/convert/'.$convert->id),
												'method'=>'delete',
												'class'=>'form-delete'
											)) }}
										<button type="submit" class="btn btn-xs btn-danger">
											<span class="glyphicon glyphicon-xs glyphicon-remove"></span> 관리전환 취소
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
							<td colspan="8">
								내역이 없습니다.
							</td>
						</tr>
					@endif
					</tbody>

				</table>
				@if ($isImport==false)
					{{ $converts->appends(array('is_import'=>'false','start'=>$start, 'end'=>$end, 'item_name'=>$itemName, 'dept_name'=>$deptName, 'checked'=>$checked ))->links() }}
				@else
					{{ $converts->appends(array('is_import'=>'true', 'start'=>$start, 'end'=>$end, 'item_name'=>$itemName, 'dept_name'=>$deptName, 'checked'=>$checked ))->links() }}
				@endif

			</div>
		</div>
	</div>
</div>

@stop
@section('scripts')
{{ HTML::datepicker() }}
@stop
