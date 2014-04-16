@extends('layouts.master')

@section('content')

<div class="row-fluid">		
	<div class="box span12">
		<div class="box-header well" data-original-title>
			<h2><i class="icon-user"></i> @lang('strings.user_list')</h2>
			<div class="box-icon">
			</div>
		</div>
		<div class="box-content">

			<div class="row-fluid">
				<div class="span12">
					<div class="datatable-controls">
						<button class="btn select-all" data-toggle="button" data-target="users-table">
							<i class="icon-check"></i> @lang('strings.select_all')
						</button>
						
						<div class="btn-group">
							<a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
								<i class="icon-edit"></i> @lang('strings.edit_status')	
								<span class="caret"></span>
							</a>
							<ul class="dropdown-menu" id="status-change-dropdown">
								<li><a href="#" data-value="1">@lang('strings.user_active')</a></li>
								<li><a href="#" data-value="0">@lang('strings.user_inactive')</a></li>
							</ul>
						</div>
						<button class="btn" id="set_groups" data-toggle="modal" href="#groups_modal">
							<i class="icon-user"></i> 그룹변경
						</button>
						<button class="btn btn-danger" id="delete-selected">
							<i class="icon-trash icon-white"></i> @lang('strings.delete')
						</button>
						<div class="pull-right">
							<a href="{{ url('admin/user/new') }}" class="btn btn-primary">
								<i class="icon-plus icon-white"></i> @lang('strings.create')
							</a>
						</div>
					</div>
					<table id="users-table" 
					class="table table-striped table-bordered table-hover bootstrap-datatable datatable multi-selectable">
						<colgroup>
					       <col span="1" style="width:50px;">
		    			</colgroup>
						<thead>
							<tr>
								<th>@lang('strings.user_id')</th>
								<th>@lang('strings.account_name')</th>
								<th>@lang('strings.user_name')</th>
								<th>@lang('strings.user_rank')</th>
								<th>@lang('strings.department')</th>
								<th>@lang('strings.dept_detail')</th>
								<th>@lang('strings.user_status')</th>
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
	</div>
</div>

<div class="modal hide" id="groups_modal">
	<div class="modal-header">
		<a class="close" data-dismiss="modal">&times;</a>
		<h3>사용자 그룹 변경</h3>
	</div>
	<div class="modal-body">
		<form id="groups_form" class="form form-horizontal">
			<div class="row-fluid">
				<div class="span12">
					<div class="control-group">
						<label for="groups" class="control-label">
							@lang('strings.groups')
						</label>
						<div class="controls">
							@foreach ($groups as $group)
							<label class="checkbox inline">
								<div class="checker">
									<span>
										<input type="checkbox" name="groups_ids[]" value="{{$group->id}}" style="opacity: 0;">
									</span>
								</div> {{ $group->name }}
						    </label>
							@endforeach
						</div>
					</div>
				</div>
			</div>

		</form>
	</div>
	<div class="modal-footer">
		<a href="#" id="set_groups_submit" class="btn btn-primary">확인</a>
		<a href="#" class="btn" data-dismiss="modal">취소</a>
	</div>
</div>
@stop

@section('scripts')
{{ HTML::script('static/js/jquery.dataTables.min.js') }}
{{ HTML::script('static/js/psis/dataTables.plugins.js') }}
<script type="text/javascript">
$(document).ready(function(){
	var oTable = $("#users-table").dataTable($.extend(dtOptions,{
				"bProcessing": true,
				"bServerSide": true,
				"sAjaxSource": "{{ action('AdminController@getUsers') }}",
				"aoColumnDefs": [
					{
						"sClass":"single-line",
						"aTargets": [0,1,2,3,4,5,6]
					},
					{
						"aTargets": [4],
						"mRender": function(data, type, full) {
							if (data)
								return $.trim(data.replace(/:/gi, " "));
							else
								return data;
						}
					},
					{
						"aTargets": [1],
						"mRender": function(data, type, full) {
							return '<a href="{{ url("admin/user") }}/'+full[0]+'"><strong>'+data+'</strong></a>';
						}
					},
					{
						"aTargets": [6],
						"mRender": function(data, type, full) {
							return data==1?
							"<span class='label label-success'>@lang('strings.user_active')</span>"
							:
							"<span class='label label-important'>@lang('strings.user_inactive')</span>";
						}
					}
				]
			}));
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
					ids.push($(this).children().eq(0).text());
				});
				$.ajax({
					url: "{{ action('AdminController@deleteUser') }}",
					type: "post",
					data: JSON.stringify(ids),
					contentType: "application/json; charset=UTF-8",
					success: function(msg) {
						if (msg)
						{
							bootbox.alert(msg);
						}
						oTable.fnDraw();
					},
					error: function() {
						bootbox.alert("@lang('stirngs.server_error')"
							);
					}
				});
			});
	});

	$("#status-change-dropdown a").click(function(){
		var status = $(this).data("value");

		var selected = fnGetSelected(oTable);
		
		if (selected.length == 0)
		{
			bootbox.alert("@lang('strings.no_selection')"
				);
			return;
		}

		var data = {
			"activated": status,
			"ids": []
		}
		selected.each(function(){
			data.ids.push($(this).children().eq(0).text());
		});

		$.ajax({
			url: "{{ action('AdminController@setUserActivated') }}",
			type: "post",
			data: JSON.stringify(data),
			contentType: "application/json; charset=UTF-8",
			success: function(msg) {
				if (msg)
				{
					bootbox.alert(msg);
				}
				oTable.fnDraw();
			},
			error: function() {
				bootbox.alert("@lang('strings.server_error')"
					);
			}
		});
	});

	$("#set_groups_submit").click(function() {
		var selected = fnGetSelected(oTable);

		var form = $("#groups_form").serializeArray();		
		if (selected.length == 0 || form.length == 0)
		{
			bootbox.alert("@lang('strings.no_selection')"
				);
			return;
		}

		var data = [];
		selected.each(function(){
			var uid = $(this).children().eq(0).text();
			data.push({"name":"user_ids[]", "value":uid});
		});

		var params = $.merge(data, form);

		$.ajax({
			url: "{{ action('AdminController@setUserGroups') }}",
			type: "post",
			data: params,
			success: function(msg) {
				if (msg)
				{
					bootbox.alert(msg);
				}
				$("#groups_modal input[type=checkbox]").prop('checked', false);
				$("#groups_modal").modal('hide');
			},
			error: function() {
				bootbox.alert("@lang('strings.server_error')"
					);
			}
		});
	});

});

</script>
@stop