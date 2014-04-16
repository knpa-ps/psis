@extends('layouts.master')

@section('content')
<div class="row-fluid">		
	<div class="box span12">
		<div class="box-header well" data-original-title>
			<h2><i class="icon-user"></i> @lang('strings.group_list')</h2>
			<div class="box-icon">
			</div>
		</div>
		<div class="box-content">
			<div class="datatable-controls">
				<button class="btn select-all" data-toggle="button" data-target="groups-table">
					<i class="icon-check"></i> @lang('strings.select_all')
				</button>
				
				<button class="btn btn-danger" id="delete-selected">
					<i class="icon-trash icon-white"></i> @lang('strings.delete')
				</button>
				<div class="pull-right">
					<a href="#" class="btn btn-primary" id="create-group">
						<i class="icon-plus icon-white"></i> @lang('strings.create')
					</a>
				</div>
			</div>
			<table id="groups-table" 
			class="table table-striped table-bordered table-hover bootstrap-datatable datatable multi-selectable">
				<thead>
					<tr>
						<th>id</th>
						<th>@lang('strings.group_name')</th>
						<th>@lang('strings.created_at')</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td class="center" colspan="100">
							{{HTML::image('static/img/ajax-loaders/ajax-loader-6.gif')}}
						</td>
					</tr>
				</tbody>
			</table>			
		</div>
	</div>
</div>

@stop

@section('scripts')
{{ HTML::script('static/js/jquery.dataTables.min.js') }}
{{ HTML::script('static/js/psis/dataTables.plugins.js') }}

<script type="text/javascript">
$(function () { 
	var oTable = $("#groups-table").dataTable($.extend(dtOptions,{
			"bProcessing": true,
			"bServerSide": true,
			"sAjaxSource": "{{ action('AdminController@getGroups') }}",
			"aoColumnDefs": [
				{
					"aTargets":[0],
					"bVisible":false
				}
			]
		}));


	$("#create-group").click(function(){
		bootbox.prompt("@lang('strings.create_group')",
		 function(groupName){
	 		$.ajax({
 				url: "{{ action('AdminController@createGroup') }}",
 				type: "post",
 				data: {"groupName":groupName},
				success: function(msg) {
					if (msg)
					{
						noty({layout:'topRight',type:'success', text:msg});
					}
					oTable.fnDraw();
				}
	 		});
		});
	});

	$("#delete-selected").click(function(){

		var selected = fnGetSelected(oTable);
		
		if (selected.length == 0)
		{
			bootbox.alert("@lang('strings.no_selection')"
				);
			return;
		}

		bootbox.confirm("@lang('strings.confirm_delete')",
			function(){
				var ids = [];
				selected.each(function(){
					ids.push(oTable.fnGetData(this)[0]);
				});
				$.ajax({
					url: "{{ action('AdminController@deleteGroup') }}",
					type: "post",
					data: JSON.stringify(ids),
					contentType: "application/json; charset=UTF-8",
					success: function(msg) {
						if (msg)
						{
							noty({layout:'topRight',type:'success', text:msg});
						}
						oTable.fnDraw();
					}
				});
			});
	});
});
</script>
@stop