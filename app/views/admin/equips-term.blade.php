@extends('layouts.master')
@section('content')
{{ HTML::style('static/vendor/bootstrap-datepicker/css/datepicker3.css') }}
<div class="row">
	<div class="col-xs-12">
		<div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title">
          <strong>장비 입력기한 관리</strong>
        </h3>
      </div>
      <div class="panel-body">
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
							<th style="text-align: center; vertical-align: middle">
								수정
							</th>
            </tr>
          </thead>
          <tbody>

							@foreach ($categories as $category)
							<tr class="group"><td colspan="5" class="group-cell">{{ $category->sort_order.'. '.$category->name }}({{sizeof($category->codes)}}종)  </td></tr>
								@foreach ($category->codes as $c)
								{{ Form::open(array(
									'url'=> 'admin/equips_term',
									'method'=>'post'
								)) }}
								<tr>
									<td colspan="3"> <a href="{{ url('equips/inventories/'.$c->code) }}">{{ $c->title }}</a> </td>
									<td>
										<input type="text" class="form-control input-datepicker input-sm" name="code">
										<input type="hidden" name="codeId" value="{{$c->id}}">
									</td>
									<td>
										<button type="submit" class="btn btn-xs btn-success">제출</button>
									</td>
								</tr>
								{{ Form::close(); }}

									@foreach ($items[$c->id] as $item)
									{{ Form::open(array(
										'url'=> 'admin/equips_term',
										'method'=>'post'
									)) }}
									<tr>
										<td> {{ substr($item->acquired_date,0,4) }}</a> </td>
										<td>
											@if ($today < ($checkPeriod[$item->id]->check_end))
											<span class="label label-success">입력가능</span>
											@else
											<span class="label label-danger">입력불가</span>
											@endif
										</td>
			              <td> {{ $item->code->title}} {{$item->maker_name}} </td>
			              <td>
			                <input type="text" class="form-control input-datepicker input-sm" name="item" value="{{$item->checkPeriod->check_end}}">
											<input type="hidden" name="itemId" value="{{$item->id}}">
			              </td>
										<td>
											<button type="submit" class="btn btn-xs btn-success">제출</button>
										</td>
			            </tr>
									{{ Form::close(); }}
									@endforeach
								@endforeach
							@endforeach

          </tbody>
        </table>
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
</script>

@stop
