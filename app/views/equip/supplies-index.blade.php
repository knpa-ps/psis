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
							<div class="col-xs-6 form-group">
								<label for="dept_name" class="control-label col-xs-3">
									관서명
								</label>
								<div class="col-xs-9">
									<input type="text" class="input-sm form-control" id="dept_name" name="dept_name" value = "{{$deptName}}">
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
								<option disabled selected>-------------14~16년도 장비만 보급할 수 있습니다-------------</option>	
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
								업체명
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
							@if($user->id==1)
							<th>
								물품ID
							</th>
							@endif
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
									<a href="{{ url('equips/supplies/'.$supply->id)}}">{{ substr($supply->acquired_date,0,4).' '.$supply->title.' '.$supply->classification}}</a>
								</td>
								<td>
									{{ $supply->maker_name }}
								</td>
								<td>
									{{ $supply->acquired_date }}
								</td>
								<td>
									{{ $supply->from_node_name }}
								</td>
								<td>
									{{-- There is decimal and larger than 1000--}}
									{{-- There is decimal and less than 1000--}}
									{{-- less than 1000--}}

									@if( preg_match('/\./', $supply->count_sum) &&  $supply->count_sum >= 1000 )
									{{ number_format($supply->count_sum, 2) }}
									@elseif( preg_match('/\./', $supply->count_sum) &&  $supply->count_sum < 1000 )
									{{ $supply->count_sum }}
									@elseif( $supply->count_sum < 1000 )
									{{ $supply->count_sum }}
									@else
									{{ number_format($supply->count_sum) }}
									@endif
								</td>
									@if($supply->from_node_id == $user->supplySet->node->id)
								<td>
								<!-- 현재 날짜 기준으로 보급일이 10일 이내인 경우만 보급취소 가능 -->
									@if( date_diff(new DateTime('now'),new DateTime($supply->supplied_date))->format('%a') < 10 )
									{{ Form::open(array(
											'url'=>url('equips/supplies/'.$supply->id),
											'method'=>'delete',
											'class'=>'form-delete'
										)) }}
										<button type="submit" class="btn btn-xs btn btn-danger btn-delete">
											<span class="glyphicon glyphicon-remove"></span> 보급취소
										</button>
									{{ Form::close() }}
									@endif
								</td>
								@else
								<td>
								</td>
								@endif
								@if($user->id==1)
								<td>
									{{ $supply->item_id }}
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
				{{ $supplies->appends(array('start'=>$start, 'end'=>$end, 'item_name'=>$itemName, 'dept_name'=>$deptName ))->links() }}
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
