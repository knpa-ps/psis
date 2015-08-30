@extends('layouts.base')
@section('styles')
<style>
body {
	background : #fff;
}
</style>
@stop
@section('body')
<div class="row">
	<div class="col-xs-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">
					<strong>{{$item->code->title}} 분실/폐기 등록</strong>
				</h3>
			</div>

			<div class="panel-body">
				{{ Form::open(array(
						'url'=> 'equips/items/'.$item->id.'/discard',
						'method'=>'post',
						'id'=>'discard_form',
						'class'=>'form-horizontal'
					)) }}
						<fieldset>
							<legend><h4>처분장비정보</h4></legend>
							<div class="form-group">
								<label for="discard_date" class="control-label col-xs-2">일자</label>
								<div class="col-xs-10">
									<input type="text" class="form-control input-datepicker input-sm " name="discard_date">
								</div>
							</div>
							<div class="form-group">
								<label for="category" class="control-label col-xs-2">유형</label>
								<div class="col-xs-10">
									<select name="category" id="category" class="form-control">
										<option value="lost">분실(보유수량 감소)</option>
										<option value="wrecked">파손장비 폐기(보유수량 및 파손수량 모두 감소)</option>
										<option value="expired">내구연한초과(보유수량 감소)</option>
									</select>
								</div>
							</div>
						</fieldset>

						<fieldset id="fieldset">
							<legend><h4>사이즈별 수량</h4></legend>
							<table class="table table-condensed table-bordered table-striped" id="count_table">
								<thead>
									<tr>
									@foreach ($item->types as $type)
										<th style='text-align: center;'>{{ $type->type_name }}</th>
									@endforeach
									</tr>
								</thead>
								<tbody id="tbody">
									<tr>
									<!-- 수량을 입력하고 수량과 함께 type_id를 hidden form을 통해 보내기 위한 폼 -->
									@foreach ($item->types as $type)
										<td>
											<input class="count" type="number" style="width:100%;" name="{{ 'type_counts['.$type->id.']' }}" placeholder="보유량 : {{ $holding[$type->id] }}">
										</td>
									@endforeach
									</tr>
								</tbody>
							</table>
						</fieldset>
						<input type="text" class="hidden" id="file_name" name="file_name">

				{{ Form::close(); }}

				<div class="col-xs-12">
					<div class="form-group">
						<label for="doc" class="control-label col-xs-2">사유서 파일</label>
						<div class="col-xs-4">
							<form id="upload_form" action="{{ url('upload/doc') }}" target="upload_target"  method="post" enctype="multipart/form-data">
								<input type="file" name="doc" id="doc" />
							</form>
						</div>
						<button class="btn btn-xs col-xs-6 btn-info" type="button" id="upload_submit"><span class="glyphicon glyphicon-upload"></span> 업로드</button>
					</div>
				</div>
				<iframe id="upload_target" name="upload_target" src="" frameborder="0" style="width:0;height:0;border:0px solid #fff;"></iframe>

				<button class="btn btn-lg btn-block btn-primary" id="submit_btn">제출</button>

			</div>
		</div>
	</div>
</div>
@stop

@section('scripts')
{{ HTML::script('static/vendor/bootstrap-datepicker/js/bootstrap-datepicker.js') }}
{{ HTML::script('static/vendor/bootstrap-datepicker/js/locales/bootstrap-datepicker.kr.js') }}
{{ HTML::script('static/vendor/bootstrap-datepicker/js/defaults.js') }}
{{ HTML::script('static/vendor/validate/jquery.validate.min.js') }}
{{ HTML::script('static/vendor/validate/additional-methods.min.js') }}
{{ HTML::script('static/vendor/validate/messages_ko.js') }}

<script type="text/javascript">
$(function(){

	$("#discard_form").validate({
		rules: {
			discard_date: {
				required: true,
				dateISO: true
			},
			category: {
				required: true,
			}
		},
		submitHandler: function(form) {
			$(".count").each(function(){
				var count = $(this).attr('value');
				if (count == '') {
					$(this).val(0);
				};
			});
		  form.submit();
		}
	});

	$("#submit_btn").on('click', function(){
		$("#discard_form").submit();
	})

	$("#upload_target").load(function() {
		var d = $(this).contents().find("#data").text();
		if (!d) {
			alert('업로드에 실패했습니다');
			return;
		}
		var result = JSON.parse(d);
		if (result.code != 0) {
			return;
		}
		alert(result.message);
		$('#file_name').val(result.fileName);
	});

	$("#upload_submit").on('click', function(){
		$("#upload_form").submit();
	})

});
</script>

@stop

@section('styles')
{{ HTML::style('static/vendor/bootstrap-datepicker/css/datepicker3.css') }}
@stop
