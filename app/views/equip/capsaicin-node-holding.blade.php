<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title"><strong>{{ $node->node_name }} {{ $year }} 캡사이신 희석액 현황</strong></h3>
	</div>
	<div class="panel-body">
		<div class="row">
			<div class="col-xs-6">
				<form>
					<div class="form-group">
						<label for="year" class="control-label">조회연도</label>
						<select name="year" id="year_select">
						@foreach ($initYears as $i)
							<option value="{{$i->year}}" {{ $i->year == $year ? 'selected' : ''}}>{{$i->year}}</option>
						@endforeach
						</select>
						<input type="hidden" name="is_state" value="true">
						<button type="submit" class="btn btn-xs btn-primary">조회</button>
					</div>
				</form>
			</div>
			<div class="col-xs-6">
				<a href="{{URL::current().'?is_state=true&export=true' }}" class="pull-right btn btn-info btn-xs"><span class="glyphicon glyphicon-download" ></span> 다운로드 (.xlsx)</a>
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
							{{ round($firstDayHolding->amount, 2) }}
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
						@if (isset($stock[$i]))
							<td colspan="2">{{ round($stock[$i], 2) }}</td>
							<td style="background-color: #FEE9FC">{{ round($usageSum[$i], 2) }}</td>
							<td style="background-color: #FEE9FC">{{ round($usageT[$i], 2) }}</td>
							<td style="background-color: #FEE9FC">{{ round($usageA[$i], 2) }}</td>
						@else
							<td></td>
							<td></td>
							<td></td>
							<td></td>
						@endif
						<td>{{ $timesSum[$i] }}</td>
						<td>{{ $timesT[$i] }}</td>
						<td>{{ $timesA[$i] }}</td>
						<td>{{ round($addition[$i], 2) }}</td>
						<td>{{ round($discard[$i], 2) }}</td>
					</tr>
					@endfor
				</tbody>
				</table>
			</div>
		</div>
	</div>
</div>