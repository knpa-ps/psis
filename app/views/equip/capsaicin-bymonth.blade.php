<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title"><strong>{{ $year }} 캡사이신 희석액 현황</strong></h3>
	</div>
	<div class="panel-body">
		<div class="well well-sm">
			<form class="form-horizontal">
				<input type="hidden" name="tab_id" value="{{$tabId}}">

				<h5>조회조건</h5>
				<div class="row">
					<div class="col-xs-6 form-group">
						<label for="year" class="control-label col-xs-3">
							조회연도
						</label>
						<div class="col-xs-9">
							<select name="year" class="input-sm form-control" id="year_select">
							@foreach ($initYears as $i)
								<option value="{{$i->year}}" {{ $i->year == $year ? 'selected' : ''}}>{{$i->year}}</option>
							@endforeach
							</select>
						</div>
					</div>
					<div class="col-xs-6 form-group">
						<label for="region" class="control-label col-xs-3">
							지방청
						</label>
						<div class="col-xs-9">
							<select name="region" class="input-sm form-control" id="region">
							<option value="1" {{ $selectedRegionId == 1 ? 'selected' : ''}} >전체</option>
							@foreach ($regions as $r)
							<option value="{{$r->id}}" {{ $r->id == $selectedRegionId ? 'selected' : ''}} >{{$r->node_name}}</option>
							@endforeach
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
		<div class="row">
			<div class="col-xs-12">
				<a href="{{URL::current().'?tab_id=2&export=true&year='.$year.'&region='.$selectedRegionId }}" class="btn btn-info btn-xs pull-right"><span class="glyphicon glyphicon-download"></span> 다운로드(.xlsx)</a>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12">
				<table class="table table-condensed table-hover table-striped table-bordered" id="capsaicin_table">
				<thead>
					<tr>
						<th rowspan="3">구분</th>
						<th colspan="2">보유량(ℓ)</th>
						<th colspan="3" style="background-color: #E89ECC">사용량(ℓ)</th>
						<th colspan="3">사용횟수</th>
						<th rowspan="2">추가량(ℓ)</th>
						<th rowspan="2">불용량(ℓ)</th>
					</tr>
					<tr>
						<th>현재보유량(ℓ)</th>
						<th>최초보유량(ℓ)</th>
						<th style="background-color: #E89ECC">계</th>
						<th style="background-color: #E89ECC">훈련시</th>
						<th style="background-color: #E89ECC">집회 시위시</th>
						<th>계</th>
						<th>훈련시</th>
						<th>집회 시위시</th>
					</tr>
					<tr>
						<th>{{  isset($presentStock) ? round($presentStock, 2) : ''}}</th>
						<th>
							{{ round($firstDayHolding, 2) }}
						</th>
						<th style="background-color: #FEE9FC">{{ round($usageSumSum, 2) }}</th>
						<th style="background-color: #FEE9FC">{{ round($usageTSum, 2) }}</th>
						<th style="background-color: #FEE9FC">{{ round($usageASum, 2) }}</th>
						<th>{{ $timesSumSum }}</th>
						<th>{{ $timesTSum }}</th>
						<th>{{ $timesASum }}</th>
						<th>{{ round($additionSum, 2) }}</th>
						<th>{{ round($discardSum, 2) }}</th>
					</tr>
				</thead>
				<tbody>
					@for ($i=1; $i <=12 ; $i++)
					<tr>
						<th style="text-align: center;">{{$i}}월</th>
						<td colspan="2">{{ isset($stock[$i]) ? round($stock[$i], 2) : '' }}</td>
						<td style="background-color: #FEE9FC">{{ isset($stock[$i]) ? round($usageSum[$i], 2) : '' }}</td>
						<td style="background-color: #FEE9FC">{{ isset($stock[$i]) ? round($usageT[$i], 2) : '' }}</td>
						<td style="background-color: #FEE9FC">{{ isset($stock[$i]) ? round($usageA[$i], 2) : '' }}</td>
						<td>{{ $timesSum[$i] }}</td>
						<td>{{ $timesT[$i] }}</td>
						<td>{{ $timesA[$i] }}</td>
						<td>{{ isset($stock[$i]) ? round($addition[$i], 2) : '' }}</td>
						<td>{{ isset($stock[$i]) ? round($discard[$i], 2) : '' }}</td>
					</tr>
					@endfor
				</tbody>
				</table>
			</div>
		</div>

	</div>
</div>