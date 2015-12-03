<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title"><strong>{{$year}}년 지방청별 캡사이신 희석액 현황</strong></h3>
	</div>
	<div class="panel-body">
		<div class="row">
			<div class="col-xs-12 toolbar-table">
				<a href="{{URL::current().'?tab_id=1&export=true' }}" class="pull-right btn btn-info btn-xs"><span class="glyphicon glyphicon-download" ></span> 다운로드 (.xlsx)</a>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12">
				<table class="table table-condensed table-hover table-striped table-bordered" id="capsaicin_table">
				<thead>
					<tr>
						<th rowspan="2">구분</th>
						<th colspan="2">보유량(ℓ)</th>
						<th colspan="3" style="background-color: #E89ECC">사용량(ℓ)</th>
						<th colspan="3">사용횟수</th>
						<th rowspan="2">타청지원(ℓ)</th>
						<th rowspan="2">추가량(ℓ)</th>
						<th rowspan="2">불용량(ℓ)</th>
					</tr>
					<tr>
						<th>현재보유량</th>
						<th>최초보유량</th>
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
						<th>{{ round($initHoldingThisYearSum,2) }}</th>
						<th style="background-color: #FEE9FC">{{ round($usageSumSum,2) }}</th>
						<th style="background-color: #FEE9FC">{{ round($usageDSum,2)}}</th>
						<th style="background-color: #FEE9FC">{{ round($usageASum,2)}}</th>
						<th>{{ $timesSumSum }}</th>
						<th>{{ $timesDSum }}</th>
						<th>{{ $timesASum }}</th>
						<th>{{ $crossUsedThisYearSum }}</th>
						<th>{{ $acquiredThisYearSum }}</th>
						<th>{{ $discardedThisYearSum }}</th>

					</tr>
				</thead>
				<tbody>
					@foreach ($nodes as $n)
					<tr>
						<th><a href="{{ url('/equips/capsaicin/node/'.$n->id).'/holding' }}">{{$n->node_name}}</a></th>
						<td>{{ round($stock[$n->id],2)}}</td>
						<td>{{ round($initHoldingThisYear[$n->id],2) }}</td>
						<td style="background-color: #FEE9FC">{{ round($usageSum[$n->id],2) }}</td>
						<td style="background-color: #FEE9FC">{{ round($usageD[$n->id], 2) }}</td>
						<td style="background-color: #FEE9FC">{{ round($usageA[$n->id], 2) }}</td>
						<td>{{ $timesSum[$n->id] }}</td>
						<td>{{ $timesD[$n->id] }}</td>
						<td>{{ $timesA[$n->id] }}</td>
						<td>{{ round($crossUsedThisYear[$n->id],2) }}</td>
						<td>{{ round($acquiredThisYear[$n->id],2) }}</td>
						<td>{{ round($discardedThisYear[$n->id],2) }}</td>
					</tr>
					@endforeach
				</tbody>
				</table>
			</div>
		</div>

	</div>
</div>
