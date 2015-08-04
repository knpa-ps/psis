@extends('layouts.master')

@section('content')

<div class="row">
	<div class="col-xs-12">
		<ul class="nav nav-tabs">
			@if ($domain == 0)
				<li class="active"><a href="{{url('equips/surveys?domain=0')}}">조사응답</a></li>
				<li><a href="{{url('equips/surveys?domain=1')}}">조사하기</a></li>
			@else
				<li><a href="{{url('equips/surveys?domain=0')}}">조사응답</a></li>
				<li class="active"><a href="{{url('equips/surveys?domain=1')}}">조사하기</a></li>
			@endif
		</ul>
		<div class="panel-default panel">
			<div class="panel-heading">
				<h3 class="panel-title">
					<strong>수요조사관리</strong>
				</h3>
			</div>
			<div class="panel-body">
				<div class="well well-sm">
					<form class="form-horizontal" id="data_table_form">
						<h5>조회조건</h5>
						<div class="row">
							<div class="col-xs-6 form-group">
								<label for="start" class="col-xs-3 control-label">
									조사일자
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
							<input type="text" class="hidden" name="domain" value="{{ $domain == 1 ? 1:0 }}">
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

				@if ($domain == 1)
				<div class="toolbar-table">
					<form action="{{url('equips/surveys/create')}}">
						<label style="margin-top: 9px; text-align: center;" for="item_to_survey" class="control-label col-xs-1">장비선택</label>
						<div class="col-xs-9">
							<select name="item" id="item_to_survey" class="form-control">
							@if(count($items)>0)
								@foreach($items as $i)
									<option value="{{$i->id}}">{{substr($i->acquired_date, 0, 4).' '.$i->code->title}} ({{$i->maker_name.' '.$i->classification}})</option>
								@endforeach
							@else
								<option value="0">올해 취득한 장비가 없습니다.</option>
							@endif
							</select>
						</div>
						
						<button type="submit" style="margin-top: 3px;" class="col-xs-2 btn-xs pull-right btn btn-success"><span class="glyphicon glyphicon-plus"></span> 수량조사 등록</button>
						<div class="clearfix"></div>
					</form>
					<!-- 수량조사 등록 폼 끝 -->
				</div>
				@endif
				<table class="table table-condensed table-bordered table-hover table-striped" id="data_table">
					<thead>
						<tr>
						<!-- 조사하기 탭 선택된 경우 -->
						@if ($domain == 1)
							<th>
								번호
							</th>
							<th>
								장비명
							</th>
							<th>
								조사일자
							</th>
							<th>
								조사응답현황
							</th>
							<th>
								작업
							</th>
						<!-- 조사응답 탭 선택된 경우 -->
						@else
							<th>
								번호
							</th>
							<th>
								장비명
							</th>
							<th>
								조사관서
							</th>
							<th>
								조사기한
							</th>
							<th>
								응답여부
							</th>
						@endif
						</tr>
					</thead>
					<tbody>
					@if (count($surveys) > 0) 
						@foreach ($surveys as $survey)
							<tr data-id="{{$survey->id}}">

								<!-- 조사하기 탭 선택된 경우 -->
								@if ($domain == 1)
								<td>
									{{ $survey->id }}
								</td>
								<td>
									<a href="{{ url('equips/surveys/'.$survey->id)}}">{{ $survey->item->code->title }}</a>
								</td>
								<td>
									{{ $survey->started_at }}
								</td>
								<td>
									{{ number_format($survey->responses->sum('count')).' / '.number_format($survey->datas->sum('count')).' ('.($survey->responses->sum('count')/$survey->datas->sum('count')*100).'%)' }}
								</td>
								<td>
									<!-- <a href="{{ url('equips/surveys/'.$survey->id.'/edit') }}" class="btn btn-xs btn-info btn-edit">
										<span class="glyphicon glyphicon-edit"></span> 수정
									</a> -->
									{{ Form::open(array(
											'url'=>url('equips/surveys/'.$survey->id),
											'method'=>'delete',
											'class'=>'form-delete'
										)) }}
										<button type="submit" class="btn btn-xs btn btn-danger btn-delete">
											<span class="glyphicon glyphicon-remove"></span> 삭제
										</button>
									{{ Form::close() }}
								</td>

								<!-- 조사응답 탭 선택된 경우 -->
								@else
								<td>
									{{ $survey->id }}
								</td>
								<td>
									<a href="{{ url('equips/surveys/'.$survey->id.'/response') }}">{{ $survey->item->code->title }}</a>
								</td>
								<td>
									{{ $survey->node->node_name }}
								</td>
								<td>
									{{ $survey->expired_at }} 까지
								</td>
								<td>
									@if ($survey->isResponsed($user->supplyNode->id))
										<span class="label label-success">설문응답완료</span>
									@else
										<span class="label label-danger">미응답</span>
									@endif
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
				{{ $surveys->links() }}
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
