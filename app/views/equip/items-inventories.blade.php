@extends('layouts.master')


@section('content')
<div class="row">
	<div class="col-xs-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">보유 현황 - <strong>{{ $item->name }}</strong></h3>
			</div>
			<div class="panel-body">

				<ul class="nav nav-tabs">
					<li class="active">
						<a href="#dept" data-toggle="tab">관서별 통계</a>
					</li>
					<li>
						<a href="#region" data-toggle="tab">지방청별 통계</a>
					</li>
				</ul>

				<div class="tab-content">
					<div class="tab-pane active" id="dept">

							<div class="row">
								<div class="col-xs-12">
									<div class="well well-small">
										<form class="form-horizontal" id="data_table_form">
											<input type="hidden" name="type" value="raw">
											<div class="row">
												<div class="col-xs-6 form-group">
													<label for="start" class="col-xs-3 control-label">
														취득일자
													</label>
													<div class="col-xs-9">
														<div class="input-daterange input-group">
														    <input type="text" class="input-sm form-control" name="start" 
														    value="{{ date('Y-m-d', strtotime('-1 month')) }}">
														    <span class="input-group-addon">~</span>
														    <input type="text" class="input-sm form-control" name="end"
														    value="{{ date('Y-m-d') }}" >
														</div>
													</div>
												</div>

												<div class="col-xs-6 form-group">
													<label for="dept_id" class="col-xs-3 control-label">
														관서
													</label>
													<div class="col-xs-9">
														{{ View::make('widget.dept-selector', array('id'=>'dept_id', 'inputClass'=>'input-sm')) }}
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
								</div>
							</div>
						<table class="table table-condensed table-bordered table-striped table-hover" id="items_table">
							<thead>
								<tr>
									<th>
										번호
									</th>
									<th>
										관서
									</th>
									<th>
										모델명
									</th>
									<th>
										보유량
									</th>
									<th>
										단위
									</th>
									<th>
										취득일자
									</th>
									<th>
										취득경로
									</th>
								</tr>
							</thead>
							<tbody>
								@foreach ($inventories as $i)
								<tr data-id="{{ $i->id }}">
									<td> {{ $i->id }} </td>
									<td> {{ $i->department->full_name }} </td>
									<td> {{ $i->model_name }} </td>
									<td> {{ $i->count }} </td>
									<td> {{ $item->unit }} </td>
									<td> {{ $i->acq_date }} </td>
									<td> {{ $i->acq_route }} </td>
								</tr>
								@endforeach
							</tbody>
						</table>
					</div>

					<div class="tab-pane" id="region">

						<div class="row">
							<div class="col-xs-12">
								<div class="well well-small">
									<form class="form-horizontal" id="data_table_form">
										<input type="hidden" name="type" value="raw">
										<div class="row">
											<div class="col-xs-6 form-group">
												<label for="start" class="col-xs-3 control-label">
													취득일자
												</label>
												<div class="col-xs-9">
													<div class="input-daterange input-group">
													    <input type="text" class="input-sm form-control" name="start" 
													    value="{{ date('Y-m-d', strtotime('-1 month')) }}">
													    <span class="input-group-addon">~</span>
													    <input type="text" class="input-sm form-control" name="end"
													    value="{{ date('Y-m-d') }}" >
													</div>
												</div>
											</div>

											<div class="col-xs-6 form-group">
												<label for="dept_id" class="col-xs-3 control-label">
													관서
												</label>
												<div class="col-xs-9">
													{{ View::make('widget.dept-selector', array('id'=>'dept_id', 'inputClass'=>'input-sm')) }}
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
							</div>
						</div>

						<table class="table table-condensed table-bordered table-striped table-hover" id="items_table">
							<thead>
								<tr>
									<th>
										지방청
									</th>
									<th>
										보유량
									</th>
									<th>
										단위
									</th>
								</tr>
							</thead>
							<tbody>
								@foreach (Department::regions()->get() as $d)

								<tr>
									<td>
										{{ $d->full_name }}
									</td>
									<td>
										{{ $d->id }}
									</td>
									<td>
										단위1
									</td>
								</tr>
								
								@endforeach
							</tbody>
						</table>
						
					</div>
				</div>


			</div>
		</div>
	</div>
</div>

@stop
@section('styles')
@stop
@section('scripts')
<script type="text/javascript">
$(function() {

});
</script>
@stop

