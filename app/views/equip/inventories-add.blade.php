@extends('layouts.master')

@section('content')
<div class="row">
	<div class="col-xs-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">
					<strong>장비취득등록</strong>
				</h3>
			</div>

			<div class="panel-body">
				{{ Form::open(array(
						'url'=> 'equips/inventories',
						'method'=>'post',
						'id'=>'basic_form',
						'class'=>'form-horizontal'
					)) }}
						<fieldset>
							<legend>
								<h4>기본정보</h4>
							</legend>

							<div class="form-group">
								<label for="item_category" class="control-label col-xs-2">분류</label>
								<div class="col-xs-10">
									<select name="item_category" id="item_category" class="form-control">
									@foreach($categories as $c)
										<option value="{{$c->id}}">{{$c->name}}</option>
									@endforeach
									</select>
								</div>
							</div>
							<div class="form-group">
								<label for="item" class="control-label col-xs-2">장비명</label>
								<div class="col-xs-10">
									<select name="item" id="item" class="form-control">
										<!-- 분류 선택시 해당 분류의 장비 목록이 option으로 들어감 -->
									</select>
								</div>
							</div>
							<div class="form-group">
								<label for="acquired_date" class="control-label col-xs-2">취득일</label>
								<div class="col-xs-10">
									<input type="text" class="form-control input-datepicker input-sm " name="acquired_date">
								</div>
							</div>
						</fieldset>
						<fieldset id="fieldset">
							<legend><h4>사이즈별 수량</h4></legend>
							<table class="table table-condensed table-bordered table-striped" id="count_table">
							<thead>
								<tr id="ths">
									<!-- ajax loaded data will set here -->
								</tr>
							</thead>
							<tbody>
								<tr id="tds">
									<!-- ajax loaded data will set here -->
								</tr>
							</tbody>
						</table>
						</fieldset>
						<button class="btn btn-lg btn-block btn-primary" type="submit">제출</button>

				{{ Form::close(); }}
				
				<div style="margin-bottom: 50px;"></div>
			</div>
		</div>
	</div>
</div>
@stop

@section('scripts')
{{ HTML::script('static/vendor/validate/jquery.validate.min.js') }}
{{ HTML::script('static/vendor/validate/additional-methods.min.js') }}
{{ HTML::script('static/vendor/validate/messages_ko.js') }}
{{ HTML::script('static/vendor/bootstrap-datepicker/js/bootstrap-datepicker.js') }}
{{ HTML::script('static/vendor/bootstrap-datepicker/js/locales/bootstrap-datepicker.kr.js') }}
{{ HTML::script('static/vendor/bootstrap-datepicker/js/defaults.js') }}

<script type="text/javascript">
$(function(){
	addRow();

	$("#item").on('change', function(){
		var selectedItemId = $("#item").attr('value');
		$("#ths").html("");
		$("#tds").html("");
		$.ajax({
			url : base_url+"/equips/inventories/create/get_item_type_set/"+selectedItemId,
			method : 'post',
			success : function(res){
				for(i=0;i<res.length;i++) {
					$("#ths").append("<th style='text-align: center;'>"+res[i].type_name+"</th>");
					$("#tds").append('<td><input type="text" style="width:100%;" name="type_counts['+i+']"><input type=text" class="hidden", name="type_ids['+i+']" value="'+res[i].id+'"></td>');
				}
			}
		});
	});

	$("#remove_detail").on('click', function(){
		removeRow();	
	});

	$("#add_details").on('click', addRow);

	function removeRow(){
		var rowNum = $("#fieldset .type_input").length;
		if (rowNum == 1) {
			alert('최소 하나의 제원을 입력해야 합니다.');
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
		row.find(".type-label").html("제원 #"+rows.length);

		row.find("input.count").prop('name', 'count['+id+']');
		row.find(".count-label").html("수량");
	}

	$('#item_category').on('change', function(){
		var data = {'id' : this.value};
		$.ajax({
			url : base_url+"/equips/inventories/create/get_items_in_category",
			method : 'post',
			data : JSON.stringify(data),
			contentType: 'application/json',
			dataType: 'json',
			success : function(res){
				var options;
				if(res.length === 0){
					$("#item").html('<option>해당 분류에 속하는 장비 없음</option>');
				}
				for (var i=0; i< res.length; i++){
					var str = '<option value="'+res[i].id+'">'+res[i].name+'</option>';
					options += str;
				}
				$("#item").html(options);
				$("#item").trigger('change');
			}
		});

		$('#item').html("<option >")
	});
	$('#item_category').trigger('change');


	$('#basic_form').validate({
		rules : {
			item_category : {
				required : true
			},
			item: {
				required : true
			},
			acquired_date : {
				required : true,
				dateISO : true
			},
		}
	});
})
</script>

@stop

@section('styles')
{{ HTML::style('static/vendor/bootstrap-datepicker/css/datepicker3.css') }}
@stop