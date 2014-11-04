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
						<th colspan="3" style="background-color: #E89ECC">사용량</th>
						<th colspan="3">사용횟수</th>
					</tr>
					<tr>
						<th style="background-color: #E89ECC">계</th>
						<th style="background-color: #E89ECC">훈련시</th>
						<th style="background-color: #E89ECC">집회시위시</th>
						<th>계</th>
						<th>훈련시</th>
						<th>집회시위시</th>
					</tr>
					<tr>
						<th>계</th>
						<th>{{ round($stockSum,2) }}</th>
						<th style="background-color: #FEE9FC">{{ round($usageSumSum,2) }}</th>
						<th style="background-color: #FEE9FC">{{ round($usageTSum,2)}}</th>
						<th style="background-color: #FEE9FC">{{ round($usageASum,2)}}</th>
						<th>{{ $timesSumSum }}</th>
						<th>{{ $timesTSum }}</th>
						<th>{{ $timesASum }}</th>
					</tr>
				</thead>
				<tbody>
					@foreach ($nodes as $n)
					<tr>
						<th><a href="{{ url('/equips/capsaicin/node/'.$n->id) }}">{{$n->node_name}}</a></th>
						<td>{{ round($stock[$n->id],2)}}</td>
						<td style="background-color: #FEE9FC">{{ round($usageSum[$n->id],2) }}</td>
						<td style="background-color: #FEE9FC">{{ round($usageT[$n->id], 2) }}</td>
						<td style="background-color: #FEE9FC">{{ round($usageA[$n->id], 2) }}</td>
						<td>{{ $timesSum[$n->id] }}</td>
						<td>{{ $timesT[$n->id]}}</td>
						<td>{{ $timesA[$n->id]}}</td>
					</tr>
					@endforeach
				</tbody>
				</table>
			</div>
		</div>

	</div>
</div>