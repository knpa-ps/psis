@extends('layouts.master')


@section('content')

<div class="row">
	<div class="col-xs-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title"><strong>{{$code->title}} 목록</strong></h3>
			</div>
			<div class="panel-body">
				@if($user->supplyNode->id === 1)
				<div class="toolbar-table">
					<a href="{{url('equips/inventories/create')}}" class="btn btn-info btn-xs pull-right">
						<span class="glyphicon glyphicon-plus"></span> 취득장비추가
					</a>	
					<div class="clearfix"></div>
				</div>
				@endif
				<table class="table table-condensed table-bordered table-striped table-hover" id="items_table">
					<thead>
						<tr>
							<th>
								연도
							</th>
							<th>
								업체명
							</th>
							<th>
								총 지급수량
							</th>
							<th>
								총 파손수량
							</th>
							<th>
								총 가용수량
							</th>
						</tr>
					</thead>
					<tbody>
						@foreach ($items as $i)
						<tr data-id="{{ $i->id }}">
							<td> {{ substr($i->acquired_date,0,4) }}</td>
							@if ($timeover[$i->id] !== 0)
								<td> <a style="color: red;" href="{{ URL::current().'/'.$i->id }}">{{ $i->maker_name.'('.$i->classification.', '.$timeover[$i->id].'년 초과)' }}</a> </td>
							@else
								<td> <a href="{{ URL::current().'/'.$i->id }}">{{ $i->maker_name }}({{$i->classification}})</a> </td>
							@endif
							
							<td> {{ $acquiredSum[$i->id] }}</td>
							<td> {{ $wreckedSum[$i->id] }} </td>
							<td> {{ $availSum[$i->id] }}</td>
						</tr>
						@endforeach
					</tbody>
				</table>

			</div>
		</div>
	</div>
</div>

@stop
@section('scripts')
{{ HTML::dataTables() }}
<script type="text/javascript">
$(function() {
	$("#items_table").DataTable({
		columnDefs: [
			{ visible: false, targets: 0 }
		],
        drawCallback: function ( settings ) {
            var api = this.api();
            var rows = api.rows( {page:'current'} ).nodes();
            var last=null;
 
            api.column(0, {page:'current'} ).data().each( function ( group, i ) {
                if ( last !== group ) {
                    $(rows).eq( i ).before(
                        '<tr class="group"><td colspan="6" class="group-cell">'+group+'</td></tr>'
                    );
 
                    last = group;
                }
            } );
        }
	});
});
</script>
@stop

