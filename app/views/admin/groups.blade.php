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
			<form class="form form-horizontal" novalidate>
			  <div class="control-group">
			    <label class="control-label">Email address</label>
			    <div class="controls">
			      <input type="email" data-validation-email-message="sdf" />
			    </div>
			  </div>
			</form>
		</div>
	</div>
</div>

@stop

@section('scripts')
{{HTML::script('static/js/jqBootstrapValidation.js')}}
<script type="text/javascript">
$(function () { 
	$("input,select,textarea").not("[type=submit]").jqBootstrapValidation(); 
});
</script>
@stop