@extends('layouts.master')


@section('content')

<div class="row">
	<div class="col-xs-12">

<ul class="nav nav-tabs">
	@foreach ($domains as $d)
		@if ($d->id == $domainId)
			<li class="active"><a href="{{url('equips/items?domain='.$d->id)}}">{{ $d->name }}</a></li>
		@else
			<li><a href="{{url('equips/items?domain='.$d->id)}}">{{ $d->name }}</a></li>
		@endif
	@endforeach
</ul>

		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title"><strong>장비 목록</strong></h3>
			</div>
			<div class="panel-body">
				<div class="toolbar-table">
					<a href="{{ url('equips/items/create') }}" class="btn btn-info btn-xs pull-right">
						<span class="glyphicon glyphicon-plus"></span> 장비추가
					</a>
					<div class="clearfix"></div>
				</div>
				<table class="table table-condensed table-bordered table-striped table-hover" id="items_table">
					<thead>
						<tr>
							<th>
								분류
							</th>
							<th>
								업체명
							</th>
							<th>
								장비명
							</th>
							<th>
								제원
							</th>
							<th>
								구입일자
							</th>
							<th>
								내구연한
							</th>
						</tr>
					</thead>
					<tbody>
						@foreach ($items as $i)
						<tr data-id="{{ $i->id }}">
							<td> {{ $i->category->name }} </td>
							<td> {{ $i->maker_name }}</td>
							<td> <a href="{{ url('equips/items/'.$i->id) }}">{{ $i->name }}</a> </td>
							<td> {{ $i->standard }} </td>
							<td> {{ $i->acquired_date }} </td>
							<td> {{ $i->persist_years }} </td>
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

