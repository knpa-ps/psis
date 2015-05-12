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
						'url'=> $mode=='create'?'equips/water_affair':'equips/water_affair/'.$water->id,
						'method'=>$mode=='create'?'post':'put',
						'id'=>'basic_form',
						'class'=>'form-horizontal'
					)) }}
						<fieldset>
							<legend>
								<h4>기본정보</h4>
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

							<div class="form-group">
								<label for="amount" class="control-label col-xs-2">사용량(ton)</label>
								<div class="col-xs-10">
									<input type="text" class="form-control input-sm" name="amount" id="amount"
									value="">
								</div>
							</div>

						</fieldset>
						<!-- 집행관서 hidden으로 -->
						<input type="hidden" name="node" value="{{$node->id}}">
						<!-- 업로드한 파일 명 hidden으로 -->
						<input type="hidden" name="file_name" value="" id="file_name">
				{{ Form::close(); }}
					<div class="form-group">
						<label align="right" for="doc" class="control-label col-xs-2">첨부문서&nbsp&nbsp</label>
						<div class="col-xs-4">
							<form id="upload_form" action="{{ url('upload/doc') }}" target="upload_target"  method="post" enctype="multipart/form-data">
								<input type="file" name="doc" id="doc" />
							</form>
						</div>
						<button class="btn btn-xs col-xs-3 btn-info" id="upload_submit"><span class="glyphicon glyphicon-upload"></span> 업로드</button>
						<a href="{{ url('/static/img/no_image_available_big.gif') }}" class="btn btn-xs col-xs-3 btn-primary"><span class="glyphicon glyphicon-download"></span> 양식 다운로드</a>
					</div>
				<iframe id="upload_target" name="upload_target" src="" frameborder="0" style="width:0;height:0;border:0px solid #fff;"></iframe>
				<input type="button" id="submit_btn" class="btn btn-lg btn-block btn-primary" value="제출">
				
				<table>
					<tbody id="template_tbody">
						<tr class="hidden unit_info">
							<td class="node_name">
								<!-- 관서명 들어감 -->
								<span class="unit_name"></span>
								<input type="text" class="hidden">
								<!-- value는 node id로 넣어줌 -->
							</td>
							<td class="amount">
								<input type="text" class="form-control input-sm input-amount" disabled>
								<!-- name은 amount[#], value는 입력한 값을 넣어줌 -->
							</td>
							<td>
								
							</td>
						</tr>
					</tbody>
				</table>
				
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
	$('#add_unit').on('click', function(){
		var length = $("#tbody .unit_info").length;
		var nodeId = $('#user_node_id').val();
		var amount = $('#amount').val();
		
		if (nodeId == '' || amount == '') {
			return alert('동원중대를 선택하고 사용량(ℓ)을 입력하세요');
		};

		var newRow = $('#template_tbody tr').clone();
		$.ajax({
			url : base_url+'/equips/get_node_name/'+nodeId,
			type : 'post',
			success : function(res) {
				newRow.find('.unit_name').html(res);
			}
		});
		newRow.removeClass('hidden');
		newRow.find('.node_name input').val(nodeId);
		newRow.find('.node_name input').attr('name', 'nodeId['+length+']');
		newRow.find('.amount input').val(amount);
		newRow.find('.amount input').attr('name', 'amount['+length+']');
		$("#tbody").append(newRow);
	});

	function removeRow(){
		var rowNum = $("#tbody .unit_info").length;
		if (rowNum == 1) {
			alert('최소 한 종류를 입력해야 합니다.');
			return;
		}

		$("#tbody .unit_info").last().remove();
	}

	$("#delete-row").on('click', function(){
		removeRow();
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
			amount: {
				required: true,
				number: true
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