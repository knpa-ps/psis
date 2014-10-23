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
								<label for="event_name" class="control-label col-xs-2">행사명</label>
								<div class="col-xs-10">
									<input type="text" class="form-control input-sm" name="event_name" id="event_name"
									value="">
								</div>
							</div>

							<div class="form-group">
								<label for="classification" class="control-label col-xs-2">행사구분</label>
								<div class="col-xs-10">
									<select name="classification" id="classification" class="form-control input-sm">
										<option value="assembly">집회</option>
										<option value="training">훈련</option>
									</select>
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
						<fieldset id="fieldset" {{ $mode=='create'? '': 'class="hidden"' }}>
							<legend><h4>동원 중대별 정보</h4>
								<span class="help-block">
									<b>캡사이신 희석액 사용현황보고 기준 예시</b><br>
									서울청 집회관리에 동원된 경기청 중대에서 사용한 경우 -> 경기청에서 서울청으로 사용 결과를 보고하고 여기에는 서울청에서 입력한다.
								</span>
							</legend>
							<table class="table table-condensed table-striped table-bordered">
								<thead>
									<tr>
										<th>중대명</th>
										<th>사용량(L)</th>
										<th>작업</th>
									</tr>
								</thead>
								<tbody id="tbody">
									<tr>
										<td>
											{{ View::make('widget.dept-selector', array('id'=>'user_node_id', 'inputClass'=>'select-node')) }}
										</td>
										<td>
											<input type="number" min="0" step="0.01" class="form-control input-sm" id="amount">
										</td>
										<td>
											<button type="button" id="add_unit" class="btn btn-xs btn-success"><span class="glyphicon glyphicon-plus"></span> 추가</button>
											<button type="button" id="delete-row" class="btn btn-xs btn-danger"><span class="glyphicon glyphicon-remove"></span> 제거</button>
										</td>
									</tr>
									
								</tbody>
							</table>
						</fieldset>
						<!-- 집행관서 hidden으로 -->
						<input type="hidden" name="node" value="{{$node->id}}">

				{{ Form::close(); }}

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
	$('#add_unit').on('click', function(){
		var length = $("#tbody .unit_info").length;
		var nodeId = $('#user_node_id').val();
		var amount = $('#amount').val();
		
		if (nodeId == '' || amount == '') {
			return alert('값을 입력하세요');
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
			}
		},
		submitHandler: function(form) {
			var basic_form = $(form);
			var len = $("#tbody .unit_info").length;
			if (len == 0) {
				return alert('최소 한개의 동원중대를 입력해야 합니다.')
			};
			$('.input-amount').removeAttr("disabled");
		    // do other things for a valid form
		    form.submit();
		}
	});
</script>
@stop

@section('styles')

{{ HTML::style('static/vendor/bootstrap-datepicker/css/datepicker3.css') }}

@stop