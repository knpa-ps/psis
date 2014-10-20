@extends('layouts.master')

@section('content')
<div class="row">
	<div class="col-xs-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">
					<strong>{{$node->node_name}} 캡사이신 사용내역 추가</strong>
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
								<h4>기본정보</h4>
							</legend>
									
							<div class="form-group">
								<label for="item_classification" class="control-label col-xs-2">행사명</label>
								<div class="col-xs-10">
									<input type="text" class="form-control input-sm" name="item_classification" id="item_classification"
									value="{{ $item->classification or '' }}">
								</div>
							</div>

							<div class="form-group">
								<label for="item_maker_name" class="control-label col-xs-2">행사구분</label>
								<div class="col-xs-10">
									<input type="text" class="form-control input-sm" name="item_maker_name" id="item_maker_name"
									value="{{ $item->maker_name or '' }}">
								</div>
							</div>
							
							<div class="form-group">
								<label for="item_maker_phone" class="control-label col-xs-2">날짜</label>
								<div class="col-xs-10">
									<input type="text" class="form-control input-sm" name="item_maker_phone" id="item_maker_phone"
									value="{{ $item->maker_phone or '' }}">
								</div>
							</div>

							<div class="form-group">
								<label for="item_acquired_date" class="control-label col-xs-2">장소</label>
								<div class="col-xs-10">
									<input type="text" class="form-control input-datepicker input-sm" name="item_acquired_date" id="item_acquired_date"
									value="{{ $item->acquired_date or '' }}">
								</div>
							</div>

						</fieldset>
						<fieldset id="fieldset" {{ $mode=='create'? '': 'class="hidden"' }}>
							<legend><h4>동원 중대별 정보</h4>
								<span class="help-block">
									<b>캡사이신 희석액 사용현황보고 기준 예시</b><br>
									서울청 집회관리에 동원된 경기청 중대에서 사용한 경우 -> 경기청에서 서울청으로 사용 결과를 보고하고 여기에는 서울청에서 입력한다.
								</span>
								<div class="form-group">
									<div class="col-xs-offset-2 col-xs-10">
										<button type="button" id="add_details" class="btn btn-sm btn-success col-xs-6"><span class="glyphicon glyphicon-plus"></span> 동원 중대 추가</button>
										<button type="button" id="remove_detail" class="col-xs-6 btn btn-sm btn-danger"><span class="glyphicon glyphicon-remove"></span> 제거</button>
									</div>
								</div>
							</legend>
						</fieldset>
						<!-- 집행관서 hidden으로 -->
						<input type="hidden" name="node" value="{{$node->id}}">

				{{ Form::close(); }}
				
				<div class="hide" id="type_template">
					<div class="form-group type_input">
						<label for="node[]" class="type-label control-label col-xs-2">동원 중대 #</label>
						<div class="col-xs-4">
							<input type="text" class="type form-control input-sm" name="node[]">
						</div>
					</div>
					<div class="form-group type_input">
						<label for="type[]" class="type-label control-label col-xs-2">중대</label>
						<div class="col-xs-4">
							<input type="text" class="type form-control input-sm" name="type[]">
						</div>
					</div>
					<div class="form-group type_input">
						<label for="type[]" class="type-label control-label col-xs-2">사용량(L)</label>
						<div class="col-xs-4">
							<input type="text" class="type form-control input-sm" name="type[]">
						</div>
					</div>
				</div>

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
{{ HTML::script('static/vendor/bootstrap-datepicker/js/bootstrap-datepicker.js') }}
{{ HTML::script('static/vendor/bootstrap-datepicker/js/locales/bootstrap-datepicker.kr.js') }}
{{ HTML::script('static/vendor/bootstrap-datepicker/js/defaults.js') }}

<script type="text/javascript">
$(function() {

	addRow();

	$("#remove_detail").on('click', function(){
		removeRow();	
	});

	$("#add_details").on('click', addRow);

	function removeRow(){
		var rowNum = $("#fieldset .type_input").length;
		if (rowNum == 1) {
			alert('최소 한 종류를 입력해야 합니다.');
			return;
		}

		$("#fieldset .type_input").last().remove();
	}

	function addRow(){
		var newRow = $("#type_template .type_input").clone();
		$("#fieldset").append(newRow);
		onRowAdded(newRow);
	}

	function onRowAdded(row) {
		var rows = $("#fieldset .type_input");
		var id = rows.length-1;
		row.find("input.type").prop('name', 'type['+id+']');
		row.find(".type-label").html("동원 중대 #"+rows.length);
	}

	$("#submit_btn").click(function() {
		$("#basic_form").submit();
	});

	$("#basic_form").validate({
		rules: {
			item_classification: {
				required: true,
				maxlength: 255
			},
			item_maker_name: {
				required: true,
				maxlength: 255
			},
			item_maker_phone: {
				required: true,
				maxlength: 255
			},
			item_acquired_date: {
				required: true,
				dateISO: true
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

{{ HTML::style('static/vendor/bootstrap-datepicker/css/datepicker3.css') }}

@stop