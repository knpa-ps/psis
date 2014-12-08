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
				<table class="table table-condensed table-bordered table-striped table-hover" id="items_table">
					<thead>
						<tr>
							<th>
								분류
							</th>
							<th>
								장비코드
							</th>
							<th>
								장비명
							</th>
						</tr>
					</thead>
					<tbody>
						@foreach ($itemCodes as $i)
						<tr data-id="{{ $i->id }}">
							<td> {{ $i->category->name }}({{sizeof($i->category->codes)}}종)</td>
							<td> {{ $i->code }} </td>
							<td> <a href="{{ url( URL::current().'/'.$i->code) }}">{{ $i->title }}</a> </td>
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

