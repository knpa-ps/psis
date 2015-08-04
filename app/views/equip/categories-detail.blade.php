@extends('layouts.master')

@section('content')

<div class="row">
	<div class="col-xs-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title"><strong>{{ $type=='create'?'장비분류추가':'장비분류수정' }}</strong></h3>
			</div>
			<div class="panel-body">
				
				{{ Form::open(array(
						'url'=> $type=='create'?'admin/categories':'admin/categories/'.$category->id,
						'method'=> $type=='create'?'post':'put',
						'id'=>'detail_form',
						'class'=>'form-horizontal'
					)) }}
					<div class="row">
						<div class="col-xs-4">
							<div class="form-group">
								<label for="domain_id" class="control-label col-xs-4">기능</label>
								<div class="col-xs-8">
									<select name="domain_id" id="domain_id" class="form-control">
										@foreach ($domains as $d)
											@if (isset($category) && $category->domain_id == $d->id)
												<option value="{{ $d->id }}" selected>{{ $d->name }}</option>
											@else
												<option value="{{ $d->id }}">{{ $d->name }}</option>
											@endif
										@endforeach
									</select>
								</div>
							</div>
						</div>
						<div class="col-xs-4">
							<div class="form-group">
								<label for="category_name" class="control-label col-xs-4">분류이름</label>
								<div class="col-xs-8">
									<input type="text" class="form-control" 
									name="category_name" id="category_name" value="{{ $category->name or '' }}">
								</div>
							</div>
						</div>
						<div class="col-xs-4">
							<div class="form-group">
								<div class="col-xs-offset-8 col-xs-2">
									<input type="submit" class="btn btn-sm btn-primary" value="제출">
								</div>
							</div>
						</div>
					</div>
				{{ Form::close() }}

			</div>
		</div>
	</div>
</div>

@stop

@section('scripts')
{{ HTML::script('static/vendor/validate/jquery.validate.min.js') }}
{{ HTML::script('static/vendor/validate/additional-methods.min.js') }}
{{ HTML::script('static/vendor/validate/messages_ko.js') }}
<script type="text/javascript">
$(function() {
	$("#detail_form").validate({
		rules: {
			category_name: {
				required: true,
				maxlength: 255
			}
		}
	});
});
</script>
@stop