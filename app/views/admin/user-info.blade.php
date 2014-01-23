@extends('layouts.master')

@section('content')
<div class="row-fluid">		
	<div class="box span12">
		<div class="box-header well" data-original-title>
			<h2><i class="icon-user"></i> @lang('strings.user_detail')</h2>
			<div class="box-icon">

			</div>
		</div>
		<div class="box-content">
		<form method="POST">
		<table class="table table-form">
			<tbody>
				<tr>
					<th>
						@lang('strings.account_name')
					</th>						
					<td colspan="3">
						{{$user->account_name}}
					</td>
				</tr>
				<tr>
					<th>
						@lang('strings.user_rank')
					</th>	
					<td>
						{{ Form::select('user_rank', $ranks, $user->user_rank) }}
					</td>
					<th>
						@lang('strings.user_name')
					</th>
					<td>
						<input type="text"
						name="user_name"
						value="{{ $user->user_name }}"
						required
						maxlength="10"
						>
					</td>
				</tr>
				<tr>
					<th>
						@lang('strings.department')
					</th>
					<td>
						<div class="input-append">
					        <span class="input-large uneditable-input" id="department-name">
					        {{ is_null($user->department)?Lang::get('strings.department'):$user->department->parseFullName()}}
					        </span><button class="btn" type="button" id="dept-search"
					            onclick="popup('{{action('DepartmentController@showDeptTree')}}', '', 500, 800)">
					            @lang('strings.search')
					        </button>
				        </div>
						{{ Form::hidden('department_id', !is_null($user->department)?$user->department->id:'') }}
					</td>
					<th>
						@lang('strings.dept_detail')
					</th>
					<td>
						<input type="text"
						name="dept_detail"
						value="{{ $user->dept_detail }}"
						maxlength="100">
					</td>
				</tr>
				<tr>
					<th>
						@lang('strings.registered_at')
					</th>
					<td>
						{{ $user->created_at }}
					</td>
					<th>
						@lang('strings.user_status')
					</th>
					<td>
						{{ Lang::get($user->activated?'strings.user_active':'strings.user_inactive') }}
					</td>
				</tr>
				<tr>
					<th colspan="4">
						@lang('strings.groups')
					</th>
				</tr>
				<tr>
					<td colspan="4">
					@foreach ($groups as $group)
						<label class="checkbox inline">
							<div class="checker">
								<span>
									<input type="checkbox" name="groups_ids[]" value="{{$group->id}}" style="opacity: 0;" 
									@foreach ($user->groups()->get() as $ug)
										@if ($ug->id == $group->id)
											checked="checked"
										@endif
									@endforeach
									>
								</span>
							</div> {{ $group->name }}
					    </label>
					@endforeach
					</td>
				</tr>
			</tbody>
		</table>
		<div class="pull-right">
			<button class="btn btn-primary" type="submit">
				@lang('strings.edit')
			</button>
		</div>
		</form>
		<div class="clearfix"></div>
		</div>
	</div>
</div>
@stop

@section('scripts')
{{HTML::script('static/js/jqBootstrapValidation.js')}}
<script type="text/javascript">
$(document).ready(function(){
	$("input,select,textarea").not("[type=submit]").jqBootstrapValidation(); 
});

</script>
@stop