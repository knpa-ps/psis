@extends('layouts.master')


@section('content')

<div class="row">
	<div class="col-xs-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title"><strong>{{$code->title}} 목록</strong></h3>
			</div>
			<div class="panel-body">
				<div class="toolbar-table">
					<a href="{{ url('admin/item_codes/create?code='.$code->code) }}" class="btn btn-info btn-xs pull-right">
						<span class="glyphicon glyphicon-plus"></span> 장비추가
					</a>
					<div class="clearfix"></div>
				</div>
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
								구분
							</th>
							<th>
								사용연한
							</th>
							<th>
								보급부서
							</th>
						</tr>
					</thead>
					<tbody>
						@foreach ($items as $i)
						<tr data-id="{{ $i->id }}">
							<td> {{ substr($i->acquired_date,0,4) }}</td>
							<td> <a href="{{ url('admin/item_codes/'.$i->id) }}">{{ $i->maker_name }}</a> </td>
							<td> {{ $i->classification }} </td>
							<td> {{ $i->persist_years }} </td>
							<td> {{ $i->supplier }} </td>
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

