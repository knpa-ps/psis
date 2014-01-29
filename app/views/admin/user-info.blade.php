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
		<form method="POST" class="form form-horizontal" novalidate>
		<div class="row-fluid">
			<div class="span12">
				<div class="control-group">
					<label for="account_name" class="control-label">@lang('strings.account_name')</label>
					<div class="controls">
						@if ($user->account_name)
						<span class="uneditable-input input-large">
							{{$user->account_name}}
						</span>	
						@else
						<input type="text" name="account_name" required minlength="4" maxlength="30"
						data-validation-ajax-ajax="{{ action('UserController@isUniqueAccountName') }}">
						@endif
					</div>
				</div>
			</div>
		</div>
		<div class="row-fluid">
			<div class="span6">
				<div class="control-group">
					<label for="password" class="control-label">
						@lang('strings.login_password')
					</label>
					<div class="controls">
						<input type="password" name="password"
						minlength="8">
					</div>
				</div>
			</div>
			<div class="span6">
				<div class="control-group">
					<label for="password" class="control-label">
						@lang('strings.password_confirmation')
					</label>
					<div class="controls">
						<input type="password" name="password_confirmation" data-validation-passwordagain="password" >	
					</div>
				</div>
			</div>
		</div>
		<div class="row-fluid">
			<div class="span6">
				<div class="control-group">
					<label for="user_rank" class="control-label">
						@lang('strings.user_rank')
					</label>
					<div class="controls">
						{{ Form::select('user_rank', $ranks, $user->user_rank?$user->user_rank:'R006',
						array(
							'data-rel'=>'chosen',
							'class'=>'input-large'
						)) }}
					</div>
				</div>
			</div>
			<div class="span6">
				<div class="control-group">
					<label for="user_name" class="control-label">@lang('strings.user_name')</label>
					<div class="controls">
						<input type="text"
						name="user_name"
						value="{{ $user->user_name }}"
						required
						maxlength="10">
					</div>
				</div>
			</div>
		</div>
		<div class="row-fluid">
			<div class="span6">
				<div class="control-group">
					<label for="department" class="control-label">
						@lang('strings.department')
					</label>
					<div class="controls">
						<div class="input-append">
					        <span class="input-large uneditable-input" id="department-name">
					        {{ is_null($user->department)?Lang::get('strings.department'):$user->department->parseFullName()}}
					        </span><button class="btn" type="button" id="dept-search"
					            onclick="popup('{{action('DepartmentController@showDeptTree')}}', '', 500, 800)">
					            @lang('strings.search')
					        </button>
				        </div>
						{{ Form::hidden('department_id', !is_null($user->department)?$user->department->id:'', 
						array('required')) }}
					</div>
				</div>
			</div>
			<div class="span6">
				<div class="control-group">
					<label for="dept_detail" class="control-label">
						@lang('strings.dept_detail')
					</label>
					<div class="controls">
						<input type="text"
						name="dept_detail"
						value="{{ $user->dept_detail }}"
						maxlength="100">
					</div>
				</div>
			</div>
		</div>
		<div class="row-fluid">
			<div class="span6">
				<div class="control-group">
					<label for="registered_at" class="control-label">
						@lang('strings.registered_at')
					</label>
					<div class="controls">
						<span class="uneditable-input input-large">
							{{ $user->created_at }}
						</span>
					</div>
				</div>
			</div>
		</div>
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
					</div>
				</div>
			</div>
		</div>

		<div class="pull-right">
			<button class="btn btn-primary" type="submit">
				@if ($user->account_name)
					@lang('strings.edit')
				@else 
					@lang('strings.create')
				@endif
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

function setDept(deptId, deptName) {
    $("span#department-name").text(deptName);
    $("input[name='department']").val(deptName);
    $("input[name='department_id']").val(deptId);
}
</script>
@stop