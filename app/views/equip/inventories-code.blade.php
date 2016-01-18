@extends('layouts.master')


@section('content')

<div class="row">
	<div class="col-xs-12">
		<ul class="nav nav-tabs">
			@if ($byYear == true)
				<li class="active"><a href="{{url('equips/inventories/'.$code->code.'?byYear=true')}}">연도별</a></li>
				<li><a href="{{url('equips/inventories/'.$code->code.'?byYear=false')}}">기관별</a></li>
			@else
				<li><a href="{{url('equips/inventories/'.$code->code.'?byYear=true')}}">연도별</a></li>
				<li class="active"><a href="{{url('equips/inventories/'.$code->code.'?byYear=false')}}">기관별</a></li>
			@endif
		</ul>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">
					<a href="{{url('equips/inventories/')}}"><span class="glyphicon glyphicon-chevron-left"></span></a><strong>{{$code->title}} 목록</strong>
				</h3>
			</div>
			<div class="panel-body">
				@if($user->supplyNode->id == 1)
				<div class="toolbar-table">
					<a href="{{url('equips/inventories/create')}}" class="btn btn-info btn-xs pull-right">
						<span class="glyphicon glyphicon-plus"></span> 취득장비추가
					</a>
					<div class="clearfix"></div>
				</div>
				@endif

				@if($byYear==false)
				<table class="table table-condensed table-bordered table-striped table-hover">
					<thead>
						<tr>
							<th style="text-align: center; vertical-align: middle" rowspan="2">
								기관명
							</th>
							<th style="text-align: center" colspan="4">
								산하 총계
							</th>
							<th colspan="4" style="background-color: #FFFCC5; text-align: center">
								기관 내 수량
							</th>
						</tr>
						<tr>
							<th style="text-align: center">
								보유
							</th>
							<th style="text-align: center">
								분실/폐기
							</th>
							<th style="text-align: center">
								파손
							</th>
							<th style="text-align: center">
								가용
							</th>
							<th style="background-color: #FFFCC5; text-align: center">
								보유
							</th>
							<th style="background-color: #FFFCC5; text-align: center">
								분실/폐기
							</th>
							<th style="background-color: #FFFCC5; text-align: center">
								파손
							</th>
							<th style="background-color: #FFFCC5; text-align: center">
								가용
							</th>
						</tr>
					</thead>
					<tbody>
						@foreach ($children as $child)
							<tr>
								<td> {{ $child->full_name }} </td>
								<td> {{ $subWreckedSumAllYear[$child->id] + $subAvailSumAllYear[$child->id] }}</td>
								<td> {{ $subDiscardSumAllYear[$child->id] }} ({{ $subDiscardSetsAllYear[$child->id] }}건) </td>
								<td> {{ $subWreckedSumAllYear[$child->id] }}</td>
								<td> {{ $subAvailSumAllYear[$child->id] }}</td>
								<td style="background-color: #FFF8E5"> {{ $wreckedSumAllYear[$child->id] + $availSumAllYear[$child->id] }}</td>
								<td style="background-color: #FFF8E5"> {{ $discardSumAllYear[$child->id] }} ({{ $discardSetsAllYear[$child->id] }}건) </td>
								<td style="background-color: #FFF8E5"> {{ $wreckedSumAllYear[$child->id] }} </td>
								<td style="background-color: #FFF8E5"> {{ $availSumAllYear[$child->id] }}</td>
							</tr>
						@endforeach
					</tbody>
				</table>
				@endif

				<hr>

				@if($user->supplyNode->type_code == ('D001' or 'D002'))
					<div class="row">
						<div class="col-xs-12 text-center">
							<input type="checkbox" name="guard" value="guard" checked="checked"> 경비
							<input type="checkbox" name="patrol" value="patrol" checked="checked"> 방순대
							<input type="checkbox" name="others" value="others" checked="checked"> 기타
						</div>
					</div>
				@endif

				<table class="table table-condensed table-bordered table-striped table-hover" id="items_tables">
					@if($byYear==true)
					<thead>
						<tr>
							<th style="text-align: center; vertical-align: middle" rowspan="2">
								연도
							</th>
							<th style="text-align: center; vertical-align: middle" rowspan="2">
								업체명
							</th>
							<th style="text-align: center" colspan="4">
								산하 총계
							</th>
							<th colspan="4" style="background-color: #FFFCC5; text-align: center">
								{{$user->supplyNode->full_name}}
							</th>
						</tr>
						<tr>
							<th style="text-align: center">
								보유
							</th>
							<th style="text-align: center">
								분실/폐기
							</th>
							<th style="text-align: center">
								파손
							</th>
							<th style="text-align: center">
								가용
							</th>
							<th style="background-color: #FFFCC5; text-align: center">
								보유
							</th>
							<th style="background-color: #FFFCC5; text-align: center">
								분실/폐기
							</th>
							<th style="background-color: #FFFCC5; text-align: center">
								파손
							</th>
							<th style="background-color: #FFFCC5; text-align: center">
								가용
							</th>
						</tr>
					</thead>

					@else
					<thead>
						<tr>
							<th style="text-align: center; vertical-align: middle" rowspan="2">
								번호
							</th>
							<th style="text-align: center; vertical-align: middle" rowspan="2">
								기관명
							</th>
							<th style="text-align: center; vertical-align: middle" rowspan="2">
								연도
							</th>
							<th style="text-align: center; vertical-align: middle" rowspan="2">
								구분
							</th>
							<th style="text-align: center" colspan="4">
								산하 총계
							</th>
							<th colspan="4" style="background-color: #FFFCC5; text-align: center">
								기관 내 수량
							</th>
						</tr>
						<tr>
							<th style="text-align: center">
								보유
							</th>
							<th style="text-align: center">
								분실/폐기
							</th>
							<th style="text-align: center">
								파손
							</th>
							<th style="text-align: center">
								가용
							</th>
							<th style="background-color: #FFFCC5; text-align: center">
								보유
							</th>
							<th style="background-color: #FFFCC5; text-align: center">
								분실/폐기
							</th>
							<th style="background-color: #FFFCC5; text-align: center">
								파손
							</th>
							<th style="background-color: #FFFCC5; text-align: center">
								가용
							</th>
						</tr>
					</thead>
					@endif

					@if($byYear==true)
					<tbody>
						@foreach ($items as $i)
						<tr data-id="{{ $i->id }}">
							<td> {{ substr($i->acquired_date,0,4) }}</td>
							@if ($timeover[$i->id] !== 0)
								<td> <a style="color: red;" href="{{ URL::current().'/'.$i->id }}">{{ $i->maker_name.'('.$i->classification.', '.$timeover[$i->id].'년 초과)' }}</a> </td>
							@else
								<td> <a href="{{ URL::current().'/'.$i->id }}">{{ $i->maker_name }}({{$i->classification}})</a> </td>
							@endif
							<td> {{ $subWreckedSum[$user->supplyNode->id][$i->id] + $subAvailSum[$user->supplyNode->id][$i->id] }}</td>
							<td> <a href="{{ URL::to('equips/items').'/'.$i->id.'/discard_list' }}" target="_blank">{{ $subDiscardSum[$user->supplyNode->id][$i->id] }} ({{ $subDiscardSets[$user->supplyNode->id][$i->id] }}건)</a> </td>
							<td> {{ $subWreckedSum[$user->supplyNode->id][$i->id] }}</td>
							<td> {{ $subAvailSum[$user->supplyNode->id][$i->id] }}</td>
							<td style="background-color: #FFF8E5"> {{ $wreckedSum[$user->supplyNode->id][$i->id] + $availSum[$user->supplyNode->id][$i->id] }}</td>
							<td style="background-color: #FFF8E5"> {{ $discardSum[$user->supplyNode->id][$i->id] }} ({{ $discardSets[$user->supplyNode->id][$i->id] }}건) </td>
							<td style="background-color: #FFF8E5"> {{ $wreckedSum[$user->supplyNode->id][$i->id] }} </td>
							<td style="background-color: #FFF8E5"> {{ $availSum[$user->supplyNode->id][$i->id] }}</td>

						</tr>
						@endforeach
					</tbody>
					@else
					<tbody>
						@foreach ($children as $child)
							<tr>
								<td> {{ $child->id }} </td>
								<td> {{ $child->full_name }} </td>
								<td> {{ 3000 }} </td>
								<td> 합계 </td>
								<td> {{ $subWreckedSumAllYear[$child->id] + $subAvailSumAllYear[$child->id] }}</td>
								<td> {{ $subDiscardSumAllYear[$child->id] }} ({{ $subDiscardSetsAllYear[$child->id] }}건) </td>
								<td> {{ $subWreckedSumAllYear[$child->id] }}</td>
								<td> {{ $subAvailSumAllYear[$child->id] }}</td>
								<td style="background-color: #FFF8E5"> {{ $wreckedSumAllYear[$child->id] + $availSumAllYear[$child->id] }}</td>
								<td style="background-color: #FFF8E5"> {{ $discardSumAllYear[$child->id] }} ({{ $discardSetsAllYear[$child->id] }}건) </td>
								<td style="background-color: #FFF8E5"> {{ $wreckedSumAllYear[$child->id] }} </td>
								<td style="background-color: #FFF8E5"> {{ $availSumAllYear[$child->id] }}</td>
							</tr>
							@foreach ($items as $i)
							<tr data-id="{{ $i->id }}">
								<td> {{ $child->id }} </td>
								<td> {{ $child->full_name }} </td>
								<td> {{ substr($i->acquired_date,0,4) }} </td>
								@if ($timeover[$i->id] !== 0)
									<td> <a style="color: red;" href="{{ URL::current().'/'.$i->id }}">{{ substr($i->acquired_date,0,4).' / '.$i->maker_name.'('.$i->classification.', '.$timeover[$i->id].'년 초과)' }}</a> </td>
								@else
									<td> <a href="{{ URL::current().'/'.$i->id }}">{{ substr($i->acquired_date,0,4).' / '.$i->maker_name }}({{$i->classification}})</a> </td>
								@endif
								<td> {{ $subWreckedSum[$child->id][$i->id] + $subAvailSum[$child->id][$i->id] }}</td>
								<td> {{ $subDiscardSum[$child->id][$i->id] }} ({{ $subDiscardSets[$child->id][$i->id] }}건) </td>
								<td> {{ $subWreckedSum[$child->id][$i->id] }}</td>
								<td> {{ $subAvailSum[$child->id][$i->id] }}</td>
								<td style="background-color: #FFF8E5"> {{ $wreckedSum[$child->id][$i->id] + $availSum[$child->id][$i->id] }}</td>
								<td style="background-color: #FFF8E5"> {{ $discardSum[$child->id][$i->id] }} ({{ $discardSets[$child->id][$i->id] }}건) </td>
								<td style="background-color: #FFF8E5"> {{ $wreckedSum[$child->id][$i->id] }} </td>
								<td style="background-color: #FFF8E5"> {{ $availSum[$child->id][$i->id] }}</td>
							</tr>
							@endforeach
						@endforeach
					</tbody>
					@endif
				</table>

			</div>
		</div>
	</div>
</div>

@stop
@section('scripts')
{{ HTML::dataTables() }}

@if($byYear==true)
<script type="text/javascript">
$(function() {
	$("#items_tables").DataTable({
		"aaSorting": [[0, 'desc']],
		"aoColumnDefs": [
			{ "bVisible": false, "aTargets": [ 0 ] },
      { "bSortable": false, "aTargets": [ 1 ] },
			{ "bSortable": false, "aTargets": [ 2 ] },
			{ "bSortable": false, "aTargets": [ 3 ] },
			{ "bSortable": false, "aTargets": [ 4 ] },
			{ "bSortable": false, "aTargets": [ 5 ] },
			{ "bSortable": false, "aTargets": [ 6 ] },
			{ "bSortable": false, "aTargets": [ 7 ] },
			{ "bSortable": false, "aTargets": [ 8 ] },
			{ "bSortable": false, "aTargets": [ 9 ] }
    ],

    drawCallback: function ( settings ) {
      var api = this.api();
      var rows = api.rows( {page:'current'} ).nodes();
      var last=null;
      api.column(0, {page:'current'} ).data().each( function ( group, i ) {
          if ( last !== group ) {
              $(rows).eq( i ).before(
                  '<tr class="group"><td colspan="9" class="group-cell">'+group+'</td></tr>'
              );
              last = group;
          }
      });
  	}
	});
});
</script>
@else
<script type="text/javascript">
$(function() {
	$("#items_tables").DataTable({
		"iDisplayLength" : -1,
		"order": [[0, 'asc'],[2, 'desc']],
		"aoColumnDefs": [
			{ "bVisible": false, "aTargets": [ 0 ] },
      { "bVisible": false, "bSortable": false, "aTargets": [ 1 ] },
			{ "bVisible": false, "bSortable": false, "aTargets": [ 2 ] },
			{ "bSortable": false, "aTargets": [ 3 ] },
			{ "bSortable": false, "aTargets": [ 4 ] },
			{ "bSortable": false, "aTargets": [ 5 ] },
			{ "bSortable": false, "aTargets": [ 6 ] },
			{ "bSortable": false, "aTargets": [ 7 ] },
			{ "bSortable": false, "aTargets": [ 8 ] },
			{ "bSortable": false, "aTargets": [ 9 ] },
			{ "bSortable": false, "aTargets": [ 10 ] },
			{ "bSortable": false, "aTargets": [ 11 ] },
    ],

    drawCallback: function ( settings ) {
      var api = this.api();
      var rows = api.rows( {page:'current'} ).nodes();
      var last=null;
      api.column(1, {page:'current'} ).data().each( function ( group, i ) {
          if ( last !== group ) {
              $(rows).eq( i ).before(
                  '<tr class="group"><td colspan="10" class="group-cell">'+group+'</td></tr>'
              );
              last = group;
          }
      });
  	}
	});
	$(document.body).on('click', '.group', function(){
		$(this).nextUntil('tr.group').slideToggle();
	});
	$(".group").trigger('click');


});
</script>
@endif
@stop
