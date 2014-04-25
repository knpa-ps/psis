@extends('layouts.master')

@section('content')


{{-- 속보 목록 --}}
<div class="row">
	<div class="col-xs-12">
		<div class="well">

			{{-- 속보 내용 조회 --}}
			@if (isset($report))
			<div class="row">
				<div class="col-xs-12">
				@include('report.content', array('report'=>$report, 'permissions'=>$permissions))
				</div>
			</div>
			@endif

			<div class="toolbar-table row">
				<div class="col-xs-6">
					<form method="GET" class="form-inline" role="form">
						<div class="form-group">
							<input type="text" class="form-control input-sm" placeholder="제목" name="q"
							value="{{ $input['q'] or '' }}">
						</div>
						<div class="btn-group">
							<button type="submit" class="btn btn-primary btn-xs">
								<span class="glyphicon glyphicon-search"></span> 검색
							</button>
							<button type="button" class="btn btn-primary btn-xs" id="advanced_search_toggle">
						  		<span class=" glyphicon glyphicon-chevron-down"></span>
						  	</button>
						</div>
					</form>
				</div>
				<div class="col-xs-6">
					<div class="btn-group pull-right">
						<a href="{{ url('reports/create') }}" class="btn btn-primary btn-xs">
							<span class="glyphicon glyphicon-pencil"></span> 작성
						</a>
					</div>
				</div>
			</div>

			<div id="advanced_search_container" class="panel panel-primary hide">
				<div class="panel-heading">
					<h3 class="panel-title pull-left"><strong>상세 검색</strong></h3>
  					<button type="button pull-right" class="close" aria-hidden="true">&times;</button>
					<div class="clearfix"></div>
				</div>
				<div class="panel-body">
					<form class="form-horizontal" role="form">

						<div class="row">
							<div class="form-group col-xs-6">
								<label for="start" class="col-xs-3 control-label">
									조회기간
								</label>
								<div class="col-xs-9">
									<div class="input-daterange input-group">
									    <input type="text" class="input-sm form-control" name="start" 
									    value="{{$input['start'] or ''}}"/>
									    <span class="input-group-addon">~</span>
									    <input type="text" class="input-sm form-control" name="end" 
									    value="{{$input['end'] or ''}}"/>
									</div>
								</div>
							</div>
							<div class="form-group col-xs-6">
								<label for="dept_id" class="col-xs-3 control-label">
									관서
								</label>
								<div class="col-xs-9">
									{{ View::make('widget.dept-selector', 
									array('id'=>'dept_id', 
										'inputClass'=>'input-sm', 
										'default'=> array(
											'id'=>isset($input['dept_id'])?$input['dept_id']:null, 
											'full_name'=>isset($input['dept_id_display'])?$input['dept_id_display']:null
										))) }}
								</div>
							</div>
						</div>

						<div class="row">
							<div class="form-group col-xs-6">
								<label for="q" class="col-xs-3 control-label">
									속보제목
								</label>
								<div class="col-xs-9">
									<input type="text" class="input-sm form-control" name="q" value="{{ $input['q'] or '' }}">
								</div>
							</div>
							<div class="form-group col-xs-6">
								<label for="q" class="col-xs-3 control-label">
									유형
								</label>
								<div class="col-xs-9">
									<div class="checkbox">
										<label>
											<input type="checkbox" value="1" name="o_region" 
											{{ isset($input['o_region']) ? 'checked="checked"' : '' }}> 지방청 작성
										</label>
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-xs-12">
								<button class="btn btn-primary btn-xs pull-right" type="submit">조회</button>
							</div>
						</div>
					</form>
				</div>
			</div>
				
			<div class="row">
				<div class="col-xs-12">
					<table class="table table-condensed table-striped table-hover table-bordered" id="reports_table">
						<colgroup>
							<col>
							<col>
							<col style="width: 200px;">
							<col style="width: 80px;">
							<col style="width: 120px;">
						</colgroup>
						<thead>
							<tr class="bg-info">
								<th style="min-width:40px;">번호</th>
								<th>제목</th>
								<th>작성처</th>
								<th>작성자</th>
								<th>작성 시간</th>
							</tr>
						</thead>
						<tbody>
						@if (count($reports) == 0)
							<tr>
								<td colspan="5">
									<p align="center">조회된 속보가 없습니다.</p>
								</td>
							</tr>
						@else
							@foreach ($reports as $r)
								<tr>
									<td>{{ $r->id }}</td>
									<td>
										@if ($r->is_new)
											<span class="label label-danger">New</span>
										@endif 
										@if ($r->is_updated)
											<span class="label label-info">Update</span>
										@endif
										<a href="{{ url('reports/list?'.http_build_query(array_merge($input, array('rid'=>$r->id)))) }}"
										class="{{ $r->has_read ? 'black' : 'text-primary' }}">
											{{ str_limit($r->title, 35) }}
										</a>
									</td>
									<td>{{ $r->department->full_name }}</td>
									<td>{{ $r->user->user_name }}</td>
									<td>{{ $r->created_at->format('Y-m-d h:i') }}</td>
								</tr>
							@endforeach
						@endif
						</tbody>
					</table>
				</div>
			</div>


			<div class="row">
				<div class="col-xs-4">
					<h3 id="reports-info">총 {{ $total }}개</h3>
				</div>
				<div class="col-xs-8">
					<div class="pull-right">
						{{ $reports->appends($input)->links() }}
					</div>
					<div class="clearfix"></div>
				</div>
			</div>
		</div>
	</div>
</div>
@stop

@section('styles')
{{ HTML::style('static/vendor/bootstrap-datepicker/css/datepicker3.css') }}
<style type="text/css" media="screen">
#advanced_search_container {
	position: absolute;
	top: 50px;
	z-index: 1000;
}
#reports-info {
	font-size: 14px;
}
</style>
@stop

@section('scripts')
{{ HTML::script('static/vendor/bootstrap-datepicker/js/bootstrap-datepicker.js') }}
{{ HTML::script('static/vendor/bootstrap-datepicker/js/locales/bootstrap-datepicker.kr.js') }}
{{ HTML::script('static/vendor/bootstrap-datepicker/js/defaults.js') }}
<script type="text/javascript">
$(function() {
	$("#advanced_search_toggle").click(function() {
		$("#advanced_search_container").toggleClass('hide');
	});
	$("#advanced_search_container .close").click(function() {
		$("#advanced_search_container").addClass('hide');
	});
});
</script>
@stop