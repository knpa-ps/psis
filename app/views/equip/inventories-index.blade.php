@extends('layouts.master')
@section('styles')
<style type="text/css">
.group-cell:hover {
	background-color: #5bc0de !important;
}
</style>
@stop
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
				<div class="row">
					<div class="col-xs-12">
						<div class="well">

						{{ Form::open(array(
							'url'=> URL::current(),
							'method'=>'get',
							'class'=>'form-horizontal'
						)) }}
							<fieldset>

								<div class="form-group">
									@if(sizeof($user->supplyNode->managedChildren) !== 0 )
									<label for="supply_node_id" class="col-xs-2 control-label">다운로드할 관서 선택</label>
									<div class="col-xs-4">
										{{ View::make('widget.dept-selector', array('id'=>'supply_node_id', 'inputClass'=>'select-node', 'initNodeId'=> $node->id , 'full_name'=>$user->supplyNode->full_name )) }}
									</div>
									@endif
									@if(sizeof($user->supplyNode->managedChildren) !== 0)
									<div class="col-xs-6">
									@else
									<div>
									@endif
										<button type="submit" class="col-xs-12 pull-right btn btn-info btn-sm"><span class="glyphicon glyphicon-download" ></span> 총괄표 다운로드 (.xlsx)</button>
									</div>
									<input type="text" class="hidden" name="export" value="true">
								</div>
							</fieldset>
						{{ Form::close() }}

						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-12">
						<table class="table table-condensed table-bordered table-striped table-hover" id="items_table">
							<thead>
								<tr>
									<th style="text-align: center; vertical-align: middle" rowspan="2">
										분류
									</th>
									<th style="text-align: center; vertical-align: middle" rowspan="2">
										번호
									</th>
									<th style="text-align: center; vertical-align: middle" rowspan="2">
										장비명
									</th>
									<th style="text-align: center" colspan="4">
										산하 총계
									</th>
									<th style="background-color: #FFFCC5; text-align: center" colspan="4" >
										{{$user->supplyNode->full_name}}
									</th>
								</tr>
								<tr>
									<th style="text-align: center">
										보유
									</th>
									<th style="text-align: center">
										@lang('equip.discard')
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
										@lang('equip.discard')
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
								@foreach ($categories as $ctgr)
									@for ($i=0; $i < sizeof($ctgr->codes); $i++)
									<tr data-id="{{ $ctgr->codes[$i]->id }}">
										<td> {{ $ctgr->sort_order.'. '.$ctgr->codes[$i]->category->name }}({{sizeof($ctgr->codes[$i]->category->codes)}}종) </td>
										<td> {{ $i+1 }} </td>
										<td> <a href="{{ url('equips/inventories/'.$ctgr->codes[$i]->code) }}">{{ $ctgr->codes[$i]->title }}</a> </td>
										<td> {{ $subWreckedSum[$ctgr->codes[$i]->id] + $subAvailSum[$ctgr->codes[$i]->id] }}</td>
										<td> {{ $subDiscardSum[$ctgr->codes[$i]->id] }} ({{ $subDiscardSets[$ctgr->codes[$i]->id] }}건)</td>
										<td> {{ $subWreckedSum[$ctgr->codes[$i]->id] }}</td>
										<td> {{ $subAvailSum[$ctgr->codes[$i]->id] }}</td>
										<td style="background-color: #FFF8E5"> {{ $wreckedSum[$ctgr->codes[$i]->id] + $availSum[$ctgr->codes[$i]->id] }}</td>
										<td style="background-color: #FFF8E5"> {{ $discardSum[$ctgr->codes[$i]->id] }} ({{ $discardSets[$ctgr->codes[$i]->id] }}건) </td>
										<td style="background-color: #FFF8E5"> {{ $wreckedSum[$ctgr->codes[$i]->id] }}</td>
										<td style="background-color: #FFF8E5"> {{ $availSum[$ctgr->codes[$i]->id] }}</td>
									</tr>
									@endfor
								@endforeach
							</tbody>
						</table>
					</div>
				</div>
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

		paging: false,

		columnDefs: [
			{ visible: false, targets: 0 },
			{ orderable: false, targets: "_all"}
		],
        drawCallback: function ( settings ) {
            var api = this.api();
            var rows = api.rows( {page:'current'} ).nodes();
            var last=null;

            api.column(0, {page:'current'} ).data().each( function ( group, i ) {
                if ( last !== group ) {
                    $(rows).eq( i ).before(
                        '<tr class="group"><td colspan="10" class="group-cell">'+group+" -"+'</td></tr>'
                    );

                    last = group;
                }
            } );
        },
        // Disable Sorting
        "bSort" : false
	});
	$(document.body).on('click', '.group', function(){
		$(this).nextUntil('tr.group').slideToggle();
		var origin = $(this).find('td').text();
		var group = origin.substring(0, origin.length-1);
		var flag = origin.substring(origin.length-1,origin.length);
		flag == '-' ? $(this).find('td').text(group+'+') : $(this).find('td').text(group+'-');
	});

	// $(".group").trigger('click');

});
</script>
@stop
