<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title"><strong>{{ $node->node_name }} 캡사이신 희석액 사용내역</strong></h3>
	</div>
	<div class="panel-body">
		<div class="well well-sm">
			<form class="form-horizontal" id="data_table_form">
				<h5>조회조건</h5>
				<div class="row">
					<div class="col-xs-6 form-group">
						<label for="start" class="col-xs-3 control-label">
							사용일자
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
						<label for="event_name" class="control-label col-xs-3">
							행사명
						</label>
						<div class="col-xs-9">
							<input type="text" class="input-sm form-control" id="event_name" name="event_name">
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-6 form-group">
						<label for="event_type" class="control-label col-xs-3">
							행사구분
						</label>
						<div class="col-xs-9">
							<select name="event_type" id="event_type" class="input-sm form-control">
								<option value="" {{$eventType == '' ? 'selected' : '' }}>전체</option>
								<option value="assembly" {{$eventType == 'assembly' ? 'selected' : '' }}>집회</option>
								<option value="training" {{$eventType == 'training' ? 'selected' : '' }}>훈련</option>
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

		<div class="toolbar-table">
			<a href="{{url('equips/capsaicin/create?nodeId='.$node->id)}}" class="btn btn-info btn-xs pull-right">
				<span class="glyphicon glyphicon-plus"></span> 사용내역 추가
			</a>	
			<div class="clearfix"></div>
		</div>
		<div class="row">
			<div class="col-xs-12">
				<table class="table table-condensed table-hover table-striped table-bordered" id="capsaicin_table">
				<thead>
					<tr>
						<th>일자</th>
						<th>관서명</th>
						<th>중대</th>
						<th>행사유형</th>
						<th>사용장소</th>
						<th>행사명</th>
						<th style="background-color: #E89ECC">사용량(ℓ)</th>
					</tr>
				</thead>
				<tbody>
					@if(sizeof($rows)==0)
					<tr>
						<td colspan="7">내역이 없습니다.</td>
					</tr>
					@else
					@foreach ($rows as $r)
					<tr>
						<td>{{ $r->date }}</td>
						<td>{{ $r->node->node_name }}</td>
						<td>{{ $r->user_node->node_name }}</td>
						<td>{{ $r->type }}</td>
						<td>{{ $r->location }}</td>
						@if($r->fileName != '')
						<td><a href="{{ url('uploads/docs/'.$r->fileName) }}">{{ $r->event_name }}</a></td>
						@else
						<td>{{ $r->event_name }}</td>
						@endif
						<td style="background-color: #FEE9FC">{{ round($r->amount,2) }}</td>
					</tr>
					@endforeach
					
					@endif
				</tbody>
				<tfoot>
					<tr>
						<td colspan="6">{{ $start }} ~ {{$end}} 총 사용량</td>
						<td>{{ round($totalUsage, 2) }}</td>
					</tr>
				</tfoot>
				{{ $rows->links() }}
				</table>
			</div>
		</div>

	</div>
</div>