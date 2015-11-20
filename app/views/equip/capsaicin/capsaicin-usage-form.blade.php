@extends('layouts.master')

@section('content')
<div class="row">
	<div class="col-xs-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">
					<strong>{{$node->node_name}} 집회시 캡사이신 사용내역 추가</strong>
				</h3>
			</div>

			<div class="panel-body">
				{{ Form::open(array(
						'url'=> $mode=='create'?'equips/capsaicin':'equips/capsaicin/'.$capsaicin->id,
						'method'=>$mode=='create'?'post':'put',
						'id'=>'basic_form',
						'class'=>'form-horizontal'
					)) }}
						<fieldset>
							<legend>
								<h4>캡사이신 사용정보</h4>
							</legend>

							<div class="form-group">
								<label for="region" class="control-label col-xs-2">집회 관할 지방청</label>
								<div class="col-xs-10">
									<select name="region" id="region" class="form-control input-sm">
										@foreach ($regions as $r)
										<option value="{{ $r->id }}" {{ $region == $r->id ? 'selected' : '' }} >{{$r->node_name}}</option>
										@endforeach
									</select>
								</div>
							</div>

							<div class="form-group">
								<label for="event" class="control-label col-xs-2">집회명</label>
								<div class="col-xs-10">
									<select name="event" id="event" class="form-control input-sm">
									</select>
								</div>
							</div>
							<div class="form-group">
								<label for="location" class="control-label col-xs-2">장소</label>
								<div class="col-xs-10">
									<input name="location" id="location" class="form-control input-sm" type="text">
								</div>
							</div>

							<div class="form-group">
								<label for="amount" class="control-label col-xs-2">사용량(ℓ)</label>
								<div class="col-xs-10">
									<input type="text" class="form-control input-sm" name="amount" id="amount">
								</div>
							</div>
						</fieldset>
						<input type="text" class="hidden" name="type" value="event">
						<input type="text" class="hidden" name="file_name" id="file_name">
						<input type="text" class="hidden" name="nodeId" value="{{$node->id}}">
				{{ Form::close(); }}

				<div class="col-xs-12">
					<div class="form-group">
						<label for="doc" class="control-label col-xs-2">사용보고서</label>
						<div class="col-xs-4">
							<form id="upload_form" action="{{ url('upload/doc') }}" target="upload_target"  method="post" enctype="multipart/form-data">
								<input type="file" name="doc" id="doc" />
							</form>
						</div>
						<button class="btn btn-xs col-xs-3 btn-info" type="button" id="upload_submit"><span class="glyphicon glyphicon-upload"></span> 업로드</button>
						<a href="{{ url('/static/Capsaicin_report_form.hwp') }}" class="btn btn-xs col-xs-3 btn-primary"><span class="glyphicon glyphicon-download"></span> 양식 다운로드</a>
					</div>
				</div>
				<iframe id="upload_target" name="upload_target" src="" frameborder="0" style="width:0;height:0;border:0px solid #fff;"></iframe>

				<input type="button" id="submit_btn" class="btn btn-lg btn-block btn-primary" value="제출">
			</div>
		</div>
	</div>
</div>
@stop
@section('scripts')
{{ HTML::script('static/vendor/jquery.form.js') }}
{{ HTML::script('static/vendor/validate/jquery.validate.min.js') }}
{{ HTML::script('static/vendor/validate/additional-methods.min.js') }}
{{ HTML::script('static/vendor/validate/messages_ko.js') }}

<script type="text/javascript">
	$("#region").on('change', function(){
		var regionId = $(this).attr('value');
		var params = { regionId: regionId };
		$.ajax({
			url : base_url+'/equips/capsaicin/get_events',
			data: params,
			type : 'post',
			success : function(res) {
				var num = res.length;
				$("#event").text("");
				for (var i = 0; i < res.length; i++) {
					$("#event")
					.append($('<option></option>')
					.attr("value",res[i].id)
					.text(res[i].date+' / '+res[i].event_name));
				};
			}
		});
	})
	$("#region").trigger('change');

	$("#submit_btn").on('click', function(){
		$("#basic_form").submit();
	})

	$("#upload_submit").on('click', function(){
		$("#upload_form").submit();
	})

	$("#basic_form").validate({
		rules: {
			region: {
				required: true,
				maxlength: 255
			},
			event: {
				required: true,
			},
			amount: {
				required: true,
				number: true,
				min: 0
			}
		},
		submitHandler: function(form) {
		    form.submit();
		}
	});

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
</script>
@stop

@section('styles')

@stop
