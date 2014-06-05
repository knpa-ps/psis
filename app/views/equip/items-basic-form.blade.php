@extends('layouts.master')

@section('content')
<div class="row">
	<div class="col-xs-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">
					<strong>{{ $mode=='create'?'장비추가':'장비수정' }}</strong>
				</h3>
			</div>

			<div class="panel-body">
				{{ Form::open(array(
						'url'=> $mode=='create'?'equips/items':'equips/items/'.$item->id,
						'method'=>$mode=='create'?'post':'put',
						'id'=>'basic_form',
						'class'=>'form-horizontal'
					)) }}
						<fieldset>
							<legend>
								<h4>기본정보</h4>
							</legend>

							<div class="form-group">
								<label for="item_name" class="control-label col-xs-2">장비명</label>
								<div class="col-xs-10">
									<input type="text" class="form-control input-sm" name="item_name" id="item_name" 
									value="{{ $item->name or '' }}">
								</div>
							</div>

							<div class="form-group">
								<label for="item_category_id" class="control-label col-xs-2">분류</label>
								<div class="col-xs-10">
									<select name="item_category_id" id="item_category_id" class="form-control">
										@foreach ($categories as $c)
										
										@if (isset($item) && $item->category_id == $c->id)
											<option value="{{ $c->id }}" selected>[{{ $c->domain->name }}] {{ $c->name }}</option>
										@else	
											<option value="{{ $c->id }}">[{{ $c->domain->name }}] {{ $c->name }}</option>
										@endif

										@endforeach
									</select>
								</div>
							</div>

							<div class="form-group">
								<label for="item_standard" class="control-label col-xs-2">제원</label>
								<div class="col-xs-10">
									<input type="text" class="form-control input-sm" name="item_standard" id="item_standard"
									value="{{ $item->standard or '' }}">
								</div>
							</div>

							<div class="form-group">
								<label for="item_unit" class="control-label col-xs-2">단위</label>
								<div class="col-xs-10">
									<input type="text" class="form-control input-sm" name="item_unit" id="item_unit"
									value="{{ $item->unit or '' }}">
								</div>
							</div>

							<div class="form-group">
								<label for="item_persist_years" class="control-label col-xs-2">내구연한</label>
								<div class="col-xs-10">
									<input type="text" class="form-control input-sm" name="item_persist_years" id="item_persist_years"
									value="{{ isset($item->persist_years) ? intval($item->persist_years) : '' }}">
								</div>

							</div>
						</fieldset>

				{{ Form::close(); }}


				@for ($i=0; $i<5; $i++)
				<form method="post" target="iframe_upload" 
				action="{{ url('upload/image') }}" 
				enctype="multipart/form-data" 
				class="form-upload form-horizontal">
					<input type="hidden" name="target" value="item_image_{{$i+1}}">

					<div class="form-group">
						<label class="control-label col-xs-2">장비사진{{$i+1}}</label>
						<div class="col-xs-4">
							<input type="file" class="input-sm input-item-image" name="image">
						</div>
						<div class="col-xs-2">
							<input type="submit" value="업로드" class="btn btn-default btn-xs btn-upload" data-target="#item_image_{{$i+1}}">
						</div>
						<div class="col-xs-2" id="item_image_{{$i+1}}">
							@if (isset($item) && $i < $item->images()->count())
								<?php $img = $item->images->get($i); ?>
								@include('equip.items-image-preview', array('image'=>$img))
							@endif
						</div>
					</div>
				</form>
				@endfor

				<input type="button" id="submit_btn" class="btn btn-lg btn-block btn-primary" value="제출">
			</div>
		</div>
	</div>
</div>
<div class="hide" id="image_field_template">
	@include('equip.items-image-preview')
</div>
<iframe id="iframe_upload" name="iframe_upload" src="" style="width:0;height:0;border:0px solid #fff;"></iframe> 
@stop

@section('scripts')
{{ HTML::script('static/vendor/jquery.form.js') }}
{{ HTML::script('static/vendor/validate/jquery.validate.min.js') }}
{{ HTML::script('static/vendor/validate/additional-methods.min.js') }}
{{ HTML::script('static/vendor/validate/messages_ko.js') }}

<script type="text/javascript">
$(function() {
	$("#submit_btn").click(function() {
		$("#basic_form").submit();
	});

	$("#basic_form").validate({
		rules: {
			item_name: {
				required: true,
				maxlength: 255
			},
			item_standard: {
				required: true,
				maxlength: 255
			},
			item_unit: {
				required: true,
				maxlength: 255
			},
			item_persist_years: {
				required: true,
				number: true,
				min: 0
			}
		},
		submitHandler: function(form) {
			var basic_form = $(form);
			$(".form-upload .item-images").each(function(){ 
				var url = $(this).val();
				if (!url) {
					return;
				}

				basic_form.append('<input type="hidden" name="item_images[]" value="'+url+'">');

			});
		    // do other things for a valid form
		    form.submit();
		}
	});

	$("#iframe_upload").load(function() {
		var d = $(this).contents().find("#data").text();
		if (!d) {
			alert('업로드에 실패했습니다');
			return;
		}
		var result = JSON.parse(d);
		if (result.code != 0) {
			alert(result.message);
			return;
		}
		var template = $("#image_field_template").html();
		$("#"+result.target).html(template);
		$("#"+result.target+" img").prop('src', result.url);
		$("#"+result.target+" .item-images").val(result.url);
	});
});
</script>
@stop

@section('styles')

{{ HTML::style('static/css/eq.css') }}
@stop