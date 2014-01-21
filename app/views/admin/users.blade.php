@extends('layouts.master')

@section('content')

<div class="row-fluid">		
	<div class="box span12">
		<div class="box-header well" data-original-title>
			<h2><i class="icon-user"></i> @lang('strings.user_list')</h2>
			<div class="box-icon">
				<a href="#" class="btn btn-setting btn-round"><i class="icon-cog"></i></a>
				<a href="#" class="btn btn-minimize btn-round"><i class="icon-chevron-up"></i></a>
				<a href="#" class="btn btn-close btn-round"><i class="icon-remove"></i></a>
			</div>
		</div>
		<div class="box-content">
			<table id="users-table" class="table table-striped table-bordered table-hover bootstrap-datatable datatable">
				<colgroup>
			       <col span="1" class="dt-col-row-selector">
			       <col span="1" style="width:50px;">
    			</colgroup>
				<thead>
					<tr>
						<th>
							<label class="checkbox inline row-selector">
								<div class="checker">
									<span class="">
										<input type="checkbox" id="select-all" style="opacity: 0;">
									</span>
								</div>
							</label>
						</th>
						<th>@lang('strings.user_id')</th>
						<th>@lang('strings.account_name')</th>
						<th>@lang('strings.user_name')</th>
						<th>@lang('strings.user_rank')</th>
						<th>@lang('strings.department')</th>
						<th>@lang('strings.dept_detail')</th>
						<th>@lang('strings.groups')</th>
						<th>@lang('strings.user_status')</th>
					</tr>
				</thead>
				<tbody>
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
$(document).ready(function(){
	$("#users-table").dataTable($.extend(dtOptions,{
				"bProcessing": true,
				"bServerSide": true,
				"sAjaxSource": "{{ url('users') }}",
				"sServerMethod": "POST",
				"aoColumnDefs": [
					{"sClass": "single-line", "aTargets": [0,1,2,3,4,5,6,7,8]}
				],
				"aaSorting":[[1,'asc']]
			}));
});
</script>
@stop