<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title"><strong>관서별 캡사이신 희석액 보유 현황</strong></h3>
			</div>
			<div class="panel-body">
				<div class="row">
					<div class="col-xs-12">
						<table class="table table-condensed table-hover table-striped table-bordered" id="capsaicin_table">
						<thead>
							<tr>
								<th rowspan="2">구분</th>
								<th rowspan="2">현재보유량</th>
								<th colspan="3">사용량</th>
								<th colspan="3">사용횟수</th>
								<th rowspan="3">비고</th>	
							</tr>
							<tr>
								<th>계</th>
								<th>훈련시</th>
								<th>집회시위시</th>
								<th>계</th>
								<th>훈련시</th>
								<th>집회시위시</th>
							</tr>
							<tr>
								<th>계</th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
							</tr>
						</thead>
						<tbody>
							@foreach ($nodes as $n)
							<tr>
								<th><a href="{{ url('/equips/capsaicin/node/'.$n->id) }}">{{$n->node_name}}</a></th>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
							</tr>
							@endforeach
						</tbody>
						</table>
					</div>
				</div>

			</div>
		</div>