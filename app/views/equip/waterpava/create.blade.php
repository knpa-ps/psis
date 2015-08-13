@extends('layouts.master')

@section('content')
<div class="row">
	<div class="col-xs-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">
					<strong>{{$node->node_name}} 살수차 사용내역 추가</strong>
				</h3>
			</div>

			<div class="panel-body">
				{{ Form::open(array(
						'url'=> $mode=='create'?'equips/water_pava':'equips/water_pava/'.$water_pava->id,
						'method'=>$mode=='create'?'post':'put',
						'id'=>'basic_form',
						'class'=>'form-horizontal'
					)) }}
						<fieldset>
							<legend>
								<h4>행사정보</h4>
							</legend>

							<div class="form-group">
								<label for="event_name" class="control-label col-xs-2">행사명</label>
								<div class="col-xs-10">
									<input type="text" class="form-control input-sm" name="event_name" id="event_name"
									value="">
								</div>
							</div>

							<div class="form-group">
								<label for="date" class="control-label col-xs-2">날짜</label>
								<div class="col-xs-10">
									<input type="text" class="form-control input-datepicker input-sm" name="date" id="date"
									value="">
								</div>
							</div>

							<div class="form-group">
								<label for="location" class="control-label col-xs-2">장소</label>
								<div class="col-xs-10">
									<input type="text" class="form-control input-sm" name="location" id="location"
									value="">
								</div>
							</div>
						</fieldset>
						<fieldset>
							<legend>
								<span style="font-size: 19px;">살수정보</span>
								<input type="checkbox" id="ispava"><span style="font-size: 12px;"> PAVA 혼합여부</span>
								<input type="checkbox" id="isdye"><span style="font-size: 12px;"> 염료 혼합여부</span>
							</legend>
							<table class="table table-condensed table-bordered table-striped" id="count_table">
								<thead>
									<tr id="table-head">
										<th style='text-align: center;'>살수량(ton)</th>
									</tr>
								</thead>
								<tbody>
									<tr id="table-body">
										<td><input style="width:100%;" type="text" name="water"></td>
									</tr>
								</tbody>
							</table>
						</fieldset>
						<input type="hidden" name="file_name" value="" id="file_name">
						<input type="hidden" name="node_id" value="{{$node->id}}">

				{{ Form::close(); }}
					<div class="row col-xs-12">

					</div>
					<hr>
				<div class="form-horizontal">
					<div class="form-group">
						<label for="doc" class="control-label col-xs-2">첨부문서</label>
						<div class="col-xs-4">
							<form id="upload_form" action="{{ url('upload/doc') }}" target="upload_target"  method="post" enctype="multipart/form-data">
								<input type="file" name="doc" id="doc" />
							</form>
						</div>
						<div class="col-xs-6">
							<button class="btn btn-xs col-xs-6 btn-info" type="button" id="upload_submit"><span class="glyphicon glyphicon-upload"></span> 업로드</button>
							<a href="{{ url('/static/img/no_image_available_big.gif') }}" class="btn btn-xs col-xs-6 btn-primary"><span class="glyphicon glyphicon-download"></span> 양식 다운로드</a>
						</div>
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
{{ HTML::script('static/vendor/bootstrap-datepicker/js/bootstrap-datepicker.js') }}
{{ HTML::script('static/vendor/bootstrap-datepicker/js/locales/bootstrap-datepicker.kr.js') }}
{{ HTML::script('static/vendor/bootstrap-datepicker/js/defaults.js') }}

<script type="text/javascript">
	var pavacheck = $("#ispava");
	var dyecheck = $("#isdye");

	if (pavacheck.is(':checked')) {
		$("#table-head").append("<th id='pava-label' style='text-align: center;'>PAVA 사용량(ℓ)</th>");
		$("#table-body").append("<td id='pava-input'><input style='width:100%;' type='text' name='pava'></td>");
	}
	if (dyecheck.is(':checked')) {
		$("#table-head").append("<th id='dye-label' style='text-align: center;'>염료 사용량(ℓ)</th>");
		$("#table-body").append("<td id='dye-input'><input style='width:100%;' type='text' name='dye'></td>");
	}

	pavacheck.on('click', function(){
		if (pavacheck.is(':checked')) {
			$("#table-head").append("<th id='pava-label' style='text-align: center;'>PAVA 사용량(ℓ)</th>");
			$("#table-body").append("<td id='pava-input'><input style='width:100%;' type='text' name='pava'></td>");
		} else {
			$("#pava-label").remove();
			$("#pava-input").remove();
		}
	})

	dyecheck.on('click', function(){
		if (dyecheck.is(':checked')) {
			$("#table-head").append("<th id='dye-label' style='text-align: center;'>염료 사용량(ℓ)</th>");
			$("#table-body").append("<td id='dye-input'><input style='width:100%;' type='text' name='dye'></td>");
		} else {
			$("#dye-label").remove();
			$("#dye-input").remove();
		}
	})

	$("#submit_btn").on('click', function(){
		$("#basic_form").submit();
	})

	$("#upload_submit").on('click', function(){
		$("#upload_form").submit();
	})

	$("#basic_form").validate({
		rules: {
			event_name: {
				required: true,
				maxlength: 255
			},
			location: {
				required: true,
				maxlength: 255
			},
			date: {
				required: true,
				dateISO: true
			},
			water: {
				required: true,
				number:true
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

{{ HTML::style('static/vendor/bootstrap-datepicker/css/datepicker3.css') }}

@stop
