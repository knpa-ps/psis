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
			<div class="row-fluid">
				<div class="span5">
					<table id="modules-table" class="table datatable single-selectable">
						<thead>
							<tr>
								<th>@lang('strings.module_name')</th>
								<th>@lang('strings.description')</th>
							</tr>
						</thead>
						<tbody>
							@foreach ($modules as $module)
								<tr id="{{ $module->id }}">
									<td>{{$module->name}}</td>
									<td>{{$module->description}}</td>
								</tr>
							@endforeach
						</tbody>
					</table>
				</div>

				<div class="span7">
					<form class="form-horizontal" id="perms-form">
						<legend>
							@lang('strings.permissions_list') 
							@if ($currentModule)
								{{ '('.$currentModule->name.')' }}
							@endif
						</legend>
						<input type="hidden" name="mid" value="{{ $currentModule?$currentModule->id:'' }}">
						@if ($permissions == null)
							@lang('strings.permissions_placeholder')
						@elseif (count($permissions) == 0)
							@lang('strings.no_configurable_permissions')
						@else
							@foreach ($permissions as $p)
								<div class="control-group">
									<label for="{{ $p->key }}" class="control-label">
										{{ $p->description }}
									</label>
									<div class="controls">
										@foreach ($groups as $group)
											<label class="checkbox inline">
												<div class="checker">
													<span>
														<input type="checkbox" name="{{ $p->key }}[]" value="{{$group->id}}" style="opacity: 0;" 
														<?php 
															$perms = $group->getPermissions();
															if (isset($perms[$p->key]) && $perms[$p->key])
															{
																echo "checked";
															}
														?>
														>
													</span>
												</div> {{ $group->name }}
										    </label>
										@endforeach
									</div>
								</div>
								
							@endforeach


							<div class="control-group">
								<button type="button" class="btn btn-primary pull-left" id="perms-save">
									@lang('strings.save')
								</button>
							</div>
						@endif
						
					</form>
				</div>
			</div>	
		</div>
	</div>
</div>

@stop

@section('styles')
<style type="text/css">
	#modules-table tr {
		cursor: pointer;
	}
	#perms-form legend {
		margin-bottom: 0;
	}
</style>
@stop

@section('scripts')
{{ HTML::script('static/js/jquery.dataTables.min.js') }}
{{ HTML::script('static/js/psis/dataTables.plugins.js') }}

<script type="text/javascript">
$(function () { 
	$("#modules-table tbody tr").click(function(){
		var id = $(this).prop('id');
		window.location = '{{ action('AdminController@showPermissions') }}?mid='+id;
	});
	$("#perms-save").click(function(){
		var perms = $("#perms-form").serializeArray();
		$.ajax({
			url: "{{ action('AdminController@updatePermissions') }}",
			type: "post",
			data: perms,
			dataType: "json",
			success: function(data) {
				noty(data);
			}
		});
	});
});
</script>
@stop