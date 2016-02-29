@extends('layouts.master')
@section('content')
{{ HTML::style('static/vendor/bootstrap-datepicker/css/datepicker3.css') }}
<div class="row">
	<div class="col-xs-12">
		<div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title">
					<h3 class="panel-title"><strong id="panel-title">{{$userNode->full_name}} 장비 입력기한 관리 <span style='color: red; font-size: 12px;' class='blink'>본청과 지방청 관리자만 볼 수 있습니다</span></strong></h3>
        </h3>
      </div>
      <div class="panel-body">
				@if($userNode->type_code == 'D001')
				<div class="row">
					<div class="col-xs-12">
						<h5><strong id="node_name"></strong></h5>
						<hr>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-3">
						<table class="table table-condensed table-hover table-striped table-bordered" id="node_table">
							<tr>
								<th style="text-align: center;">지방청 선택</th>
							</tr>
							@foreach ($regions as $r)
							<tr>
								<td style="text-align: center;"><a href="#" id="{{$r->id}}" class="region">{{$r->node_name}}</a></td>
							</tr>
							@endforeach
						</table>
					</div>
					<div class="col-xs-9">
		        <table class="table table-condensed table-bordered table-striped table-hover" id="items_table">
		          <thead>
		            <tr style="background-color: #F5F5F5;">
									<th style="text-align: center; vertical-align: middle">
										연도
									</th>
									<th style="text-align: center; vertical-align: middle">
										입력 가능 여부
									</th>
									<th style="text-align: center; vertical-align: middle">
										장비명
									</th>
		              <th style="text-align: center; vertical-align: middle">
										기한
									</th>
		            </tr>
		          </thead>
		          <tbody>
								{{ Form::open(array(
									'id'=>'term_form',
									'url'=> 'equips/equips_term',
									'method'=>'post'
								)) }}
									@foreach ($categories as $category)
									<tr class="group"><td colspan="5" class="group-cell">{{ $category->sort_order.'. '.$category->name }}({{sizeof($category->codes)}}종) </td></tr>
										<tr>
											<td colspan="3">
												{{ $category->name}}({{sizeof($category->codes)}}종)
											</td>
											<td>
												<input type="text" class="form-control input-datepicker input-sm category" id="category_{{$category->id}}" name="category_{{$category->id}}">
											</td>
										@foreach ($category->codes as $c)
										<tr>
											<td colspan="3"> <a href="{{ url('equips/inventories/'.$c->code) }}">{{ $c->title }}</a> </td>
											<td>
												<input type="text" class="form-control input-datepicker input-sm code" id="code_{{$c->id}}" name="code_{{$c->id}}">
											</td>
										</tr>
											@foreach ($items[$c->id] as $item)
											<tr>
												<td style="text-align: center;"> {{ substr($item->acquired_date,0,4) }}</a> </td>
												<td style="text-align: center;">
													<span id="label_{{$item->id}}"></span>
												</td>
					              <td> {{ $item->code->title}} {{$item->maker_name}} </td>
					              <td>
					                <input type="text" class="form-control input-datepicker input-sm" id="item_{{$item->id}}" name="item_{{$item->id}}" value="">
					              </td>
					            </tr>

											@endforeach
										@endforeach
									@endforeach
									<input type="hidden" name="node_id" id="node_id" value="">
								{{ Form::close(); }}

		          </tbody>
		        </table>
					</div>
				</div>
				@else
				<div class="row">
					<div class="col-xs-12">
		        <table class="table table-condensed table-bordered table-striped table-hover" id="items_table">
		          <thead>
		            <tr style="background-color: #F5F5F5;">
									<th style="text-align: center; vertical-align: middle">
										연도
									</th>
									<th style="text-align: center; vertical-align: middle">
										입력 가능 여부
									</th>
									<th style="text-align: center; vertical-align: middle">
										장비명
									</th>
		              <th style="text-align: center; vertical-align: middle">
										기한
									</th>
		            </tr>
		          </thead>
		          <tbody>
								{{ Form::open(array(
									'id'=>'term_form',
									'url'=> 'equips/equips_term',
									'method'=>'post'
								)) }}
									@foreach ($categories as $category)
									<tr class="group"><td colspan="5" class="group-cell">{{ $category->sort_order.'. '.$category->name }}({{sizeof($category->codes)}}종) </td></tr>
										<tr>
											<td colspan="3">
												{{ $category->name}}({{sizeof($category->codes)}}종)
											</td>
											<td>
												<input type="text" class="form-control input-datepicker input-sm category" id="category_{{$category->id}}" name="category_{{$category->id}}">
											</td>
										@foreach ($category->codes as $c)
										<tr>
											<td colspan="3"> <a href="{{ url('equips/inventories/'.$c->code) }}">{{ $c->title }}</a> </td>
											<td>
												<input type="text" class="form-control input-datepicker input-sm code" id="code_{{$c->id}}" name="code_{{$c->id}}">
											</td>
										</tr>
											@foreach ($items[$c->id] as $item)
											<tr>
												<td style="text-align: center;"> {{ substr($item->acquired_date,0,4) }}</a> </td>
												<td style="text-align: center;">
													@if ($today < ($checkPeriod[$item->id]->check_end))
														<span class="label label-success">입력가능</span>
													@else
														<span class="label label-danger">입력불가</span>
													@endif
												</td>
					              <td> {{ $item->code->title}} {{$item->maker_name}} </td>
					              <td>
					                <input type="text" class="form-control input-datepicker input-sm" id="item_{{$item->id}}" name="item_{{$item->id}}" value="{{$checkPeriod[$item->id]->check_end}}">
					              </td>
					            </tr>

											@endforeach
										@endforeach
									@endforeach
									<input type="hidden" name="node_id" id="node_id" value="{{$userNode->id}}">
								{{ Form::close(); }}

		          </tbody>
		        </table>
					</div>
				</div>
				@endif
				<div class="row">
					<button type="submit" class="btn btn-lg btn-block btn-primary" id="submit_btn"> 제출</button>
				</div>
      </div>
    </div>
  </div>
</div>

@stop
@section('scripts')
{{ HTML::script('static/vendor/bootstrap-datepicker/js/bootstrap-datepicker.js') }}
{{ HTML::script('static/vendor/bootstrap-datepicker/js/locales/bootstrap-datepicker.kr.js') }}
{{ HTML::script('static/vendor/bootstrap-datepicker/js/defaults.js') }}
<script type="text/javascript">

$(document.body).on('click', '.group', function(){
	$(this).nextUntil('tr.group').slideToggle();
	var origin = $(this).find('td').text();
	var group = origin.substring(0, origin.length-1);
	var flag = origin.substring(origin.length-1,origin.length);
	flag == '+' ? $(this).find('td').text(group+'-') : $(this).find('td').text(group+'+');
});

$(".group").trigger('click');

$("#submit_btn").on('click', function() {
	var formData = $("#term_form").serialize();
	$.ajax({
		url : base_url+"/equips/equips_term",
		data : formData,
		type : "post",
		success: function(res){
			alert(res);
			location.reload();
		}
	});
});

$(".region").on('click', function(){

	var regionId = $(this).attr('id');
	var params = { regionId: regionId };
	var currentTime = new Date();
	$.ajax({
		url: url("equips/get_equips_term"),
		type: "post",
		data: params,
		dataType: 'json',//내부망에선 이걸 추가해줘야 돌아감
		success: function(data){

			$.each(data[0], function(key, value) {
				var str = value.split("-");
				var date = new Date(str[0],str[1]-1,str[2]); // new Date(2013, 13, 1) is equivalent to new Date(2014, 1, 1), both create a date for 2014-02-01 (note that the month is 0-based)

				$("#item_"+key).val(value);
				if(currentTime > date) {
					$("#label_"+key).text("입력불가").removeClass('label-success').addClass("label label-danger");
				} else {
					$("#label_"+key).text("입력가능").removeClass('label-danger').addClass("label label-success");
				}
			});
			//카테고리 및 코드에 입력하는 값은 지방청을 바꾸면 초기화된다
			$(".category").val("");
			$(".code").val("");

			$("#node_name").text(data[1]+" 장비 입력기한 관리");
			$("#node_id").val(data[2]);
		}
	});
});
	$(".region").first().trigger("click");
</script>

@stop
