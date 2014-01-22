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
						<button class="btn btn-danger" id="delete-selected">
							<i class="icon-trash icon-white"></i> @lang('strings.delete')
						</button>
						<div class="pull-right">
							<a href="#create-modal" class="btn btn-primary" id="create-new" data-toggle="modal">
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

<div class="modal hide fade" id="create-modal">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal">×</button>
		<h3>@lang('strings.create_user')</h3>
	</div>
	{{ Form::open(array('action'=>'AdminController@showUserList', 'method'=>'post', 'class'=>'form form-modal', 'role'=>'form')) }}
	<div class="modal-body">
        @if (isset($messages) && !empty($messages))
        <div class="alert alert-danger">
            @foreach ($messages as $m)
                <p>{{ $m }}</p>
            @endforeach
        </div>
        @endif
        {{ Form::text('account_name', $accountName, array('class'=>'form-control',
                                        'placeholder'=>Lang::get('labels.login_account_name'),
                                        'required'=>'',
                                        'autofocus'=>'' )) }}
        <p class="help-block">@lang('strings.account_name_help')</p>

        {{ Form::password('password', array('class'=>'form-control input-medium',
                                                'placeholder'=>Lang::get('labels.login_password'),
                                                'required'=>'')) }}
        {{ Form::password('password_confirmation', array('class'=>'form-control input-medium',
                                                'placeholder'=>Lang::get('labels.password_confirmation'),
                                                'required'=>'')) }}
        <p class="help-block">@lang('strings.password_help')</p>

        {{ Form::label('user_rank', Lang::get('labels.user_rank'), array('class'=>'control-label') ) }}
        {{ Form::select('user_rank', $codeSelectItems, $userRank, array(
            'class'=>'form-control'
        )) }}
        <br>
        {{ Form::text('user_name', $userName, array('class'=>'form-control',
                                                'placeholder'=>Lang::get('labels.user_name'),
                                                'required'=>'')) }}
        <br>
        <div class="input-append">
        <span class="input-large uneditable-input" id="department-name">{{ $departmentName==''?Lang::get('strings.department'):$departmentName}}
        </span><button class="btn" type="button" id="dept-search"
            onclick="popup('{{action('DepartmentController@showDeptTree')}}', '', 500, 800)">@lang('strings.search')</button>
        </div>
        {{ Form::hidden('department', $departmentName) }}
        {{ Form::hidden('department_id', $departmentId) }}
        {{ Form::text('dept_detail', $userName, array('class'=>'form-control',
                                                'placeholder'=>Lang::get('strings.dept_detail'))) }}
    </div>
	<div class="modal-footer">
		<a href="#" class="btn" data-dismiss="modal">@lang('strings.cancel')</a>
		{{ Form::button(Lang::get('strings.create'), array('class'=>'btn btn-lg btn-primary btn-block', 'type'=>'submit')) }}
	</div>
    {{ Form::close() }}	
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
						"aTargets": [1],
						"mRender": function(data, type, full) {
							return '<a href="{{ url("admin/user") }}/'+full[0]+'">'+data+'</a>';
						}
					},
					{
						"aTargets": [6],
						"mRender": function(data, type, full) {
							return data?
							"<span class='label label-success'>@lang('strings.user_active')</span>"
							:
							"<span class='label label-important'>@lang('strings.user_inactive')</span>";
						}
					}
				]
			}));

	$("#edit-selected").click(function(){

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
				bootbox.alert("@lang('stirngs.server_error')"
					);
			}
		});
	});

	@if ($showCreateModal)
	$("#create-modal").show();
	$("#create-modal").modal('show');
	@endif
});

function setDept(deptId, deptName) {
    $("span#department-name").text(deptName);
    $("input[name='department']").val(deptName);
    $("input[name='department_id']").val(deptId);
}

</script>
@stop