@extends('layouts.master')

@section('content')
<div class="row">
	<div class="col-xs-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">
				 	<a href="{{url('equips/inventories/'.$item->item_code)}}"><span class="glyphicon glyphicon-chevron-left"></span></a> <strong>장비상세정보</strong>
				</h3>
			</div>

			<div class="panel-body">
				{{-- 기본정보 --}}
				<div class="row">
					<div class="col-xs-6">
						<h4 class="block-header"><small>{{ $category->name }}</small> {{ $item->code->title }}</h4>
						<input type="hidden" id="item_id" value="{{ $item->id }}">
					</div>

					<div class="col-xs-6">
						<div class="pull-right">
							@if(Sentry::getUser()->supplyNode->type_code == "D001")
							<a href="{{url('admin/item/'.$item->id.'/edit')}}" class="btn btn-xs btn-success">
								<span class="glyphicon glyphicon-edit"></span> 수정
							</a>
							<a href="#" class="btn btn-xs btn-danger" id="delete_btn">
								<span class="glyphicon glyphicon-trash"></span> 일괄폐기
							</a>
							@endif
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-4">
						{{-- 장비 이미지 Carousel --}}
						<div class="carousel slide" data-ride="carousel" id="item_images_carousel">
							<ol class="carousel-indicators">
								@foreach ($item->images as $idx=>$img)
								<li data-target="#item_images_carousel" data-slide-to="{{ $idx }}" class="{{ $idx==0?'active':'' }}"></li>
								@endforeach
							</ol>

							<div class="carousel-inner">
								@if ($item->images->count() == 0)
									<div class="item active">
										<img src="{{ url('/static/img/no_image_available_big.gif') }}" alt=""  />
									</div>
								@else
									@foreach ($item->images as $idx=>$i)

									<div class="item {{ $idx==0?'active':'' }}">
										<a class="fancybox" rel="gallery" href="{{ url('/uploads/'.$i->url)}}" >
											<img src="{{ url('/uploads/'.$i->url)}}" alt="" style="width:100%;" />
										</a>
									</div>

									@endforeach
								@endif
							</div>
						</div>
					</div>
					<div class="col-xs-8">
						<table class="table table-bordered table-condensed table-info" id="basic_info_table">
							<colgroup>
								<col class="col-xs-1">
								<col class="col-xs-2">
								<col class="col-xs-1">
								<col class="col-xs-2">
								<col class="col-xs-1">
								<col class="col-xs-2">
							</colgroup>
							<tbody>
								<tr>
									<th>장비명</th>
									<td colspan="5"><strong> {{ $item->code->title }} </strong></td>
								</tr>
								<tr>
									<th>보급부서</th>
									<td> {{ $item->supplier }} </td>
									<th>분류</th>
									<td colspan="3"> {{ $category->name }} </td>
								</tr>
								<tr>
									<th>제조사명</th>
									<td> {{$item->maker_name}} </td>
									<th>연락처</th>
									<td>{{ $item->maker_phone }}</td>
									<th>내구연한</th>
									<td> {{ $item->persist_years }} </td>
								</tr>
								<tr>
									<th>구분</th>
									<td colspan="3">{{$item->classification}}</td>
									<th>납품일자</th>
									<td>{{$item->acquired_date}}</td>
								</tr>
							</tbody>
						</table>

						{{-- 기타정보 목록 --}}
						<div class="row">
							<div class="col-xs-6">
								<h5 class="block-header">기타정보</h5>
							</div>

							<div class="col-xs-6">
								<a href="#" data-item-id="{{ $item->id }}" id="item_detail_btn" class="btn btn-xs btn-default pull-right">
									<span class="glyphicon glyphicon-share-alt"></span> 자세히
								</a>
							</div>
						</div>

						<table class="table table-condensed table-hover table-bordered table-striped" id="details_table">
							<colgroup>
								<col class="col-xs-8">
								<col class="col-xs-4">
							</colgroup>
							<thead>
								<tr>
									<th>제목</th>
									<th>날짜</th>
								</tr>
							</thead>
							<tbody>
								@foreach ($item->details()->orderBy('created_at', 'desc')->take(5)->get() as $detail)
								<tr>
									<td> <a href="#" class="detail-title" data-id="{{$detail->id}}" data-item-id="{{$item->id}}">{{$detail->title}}</a> </td>
									<td> {{$detail->created_at->format('Y-m-d')}} </td>
								</tr>
								@endforeach
							</tbody>
						</table>
						{{-- 기타정보 목록 끝 --}}
					</div>
				</div>{{-- 기본정보 끝 --}}

				<div class="row" style="margin-top: 15px;">
					<div class="col-xs-3">
						<h4 class="block-header">보유현황</h4>
					</div>
					<div class="col-xs-9">
						@if($modifiable)
						<button class="btn btn-xs btn-warning pull-right" id="count_update_btn"><span class="glyphicon glyphicon-pencil"></span> 보유수량 수정</button>
						@endif
						<button class="btn btn-xs btn-success pull-right" id="wrecked_update_btn"><span class="glyphicon glyphicon-pencil"></span> 파손수량 수정</button>
						<button class="btn btn-xs btn-danger pull-right" id="discard_register_btn"><span class="glyphicon glyphicon-ok"></span> 분실/폐기내역 등록</button>
						<button class="btn btn-xs btn-default pull-right" id="discard_history_btn"><span class="glyphicon glyphicon-th-list"></span> 분실/폐기내역
							@if (isset($discardSets))
								({{$discardSets}}건)
							@endif
						</button>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-12">

						<table class="table table-condensed table-bordered table-striped" style="table-layout: fixed;">
							<thead>
								<tr>
									<td style="text-align: center;"><b>구분</b></td>
								<td style="text-align: center;"><b>합계</b></td>
									@foreach($types as $t)
										<td style="text-align: center;"><b>{{$t->type_name}}</b></td>
									@endforeach
								</tr>
							</thead>
							@if ($inventorySet !== null)
								<tbody>
									<tr>
										<td style="text-align: center;"><b>보유</b></td>
										<td style="text-align: center;">{{ $inventorySet->children->sum('count') }}</td>
										@if($modifiable)
											{{ Form::open(array(
													'url'=>'/equips/items/'.$item->id.'/count_update',
													'method'=>'post',
													'id'=>'count_update_form'
												))}}
											@if($item->item_code == "M004")
											@foreach ($item->types as $type)
												<td style="text-align: center;">
													<input type="text" class="form-control input-sm positive" name="{{ 'count['.$type->id.']'}}" value="{{ $holding[$type->id] }}">
												</td>
											@endforeach
											@else
											@foreach ($item->types as $type)
												<td style="text-align: center;">
													<input type="text" class="form-control input-sm positive-int" name="{{ 'count['.$type->id.']'}}" value="{{ $holding[$type->id] }}">
												</td>
											@endforeach
											@endif

											{{ Form::close() }}
										@else
											@foreach($inventorySet->children as $c)
												<td style="text-align: center;">{{$c->count}}</td>
											@endforeach
										@endif
									</tr>
									<tr>
										<td style="text-align: center;"><b>파손</b></td>
										<td style="text-align: center;">{{ $inventorySet->children->sum('wrecked') }}</td>
										{{ Form::open(array(
											'url'=> '/equips/items/'.$item->id.'/wrecked_update',
											'method'=> 'post',
											'id'=>'wrecked_update_form'
										)) }}
										@if($item->item_code == "M004")
										@foreach ($item->types as $type)
											<td style="text-align: center;">
												<input type="text" class="form-control input-sm positive" name="{{ 'wrecked['.$type->id.']' }}" value="{{ $wrecked[$type->id] }}">
											</td>
										@endforeach
										@else
										@foreach ($item->types as $type)
											<td style="text-align: center;">
												<input type="text" class="form-control input-sm positive-int" name="{{ 'wrecked['.$type->id.']' }}" value="{{ $wrecked[$type->id] }}">
											</td>
										@endforeach
										@endif
										{{ Form::close() }}

									</tr>
								</tbody>
								<tfoot>
									<tr>
										<td style="text-align: center;"><b>가용</b></td>
										<td style="text-align: center">{{ $inventorySet->children->sum('count') - $inventorySet->children->sum('wrecked') }}</td>
										@foreach ($inventorySet->children as $c)
											<td style="text-align: center">{{ $c->count - $c->wrecked }}</td>
										@endforeach
									</tr>
								</tfoot>
							@else
								<tr>
									<td colspan="{{ sizeof($types)+2 }}" align="center">보유수량이 없습니다.</td>
								</tr>
							@endif
						</table>
					</div>
				</div>
				<div class="row" id="data_container">
					<div class="col-xs-12">
						<h4>하위부서 보유현황</h4>

						<table style="table-layout: fixed; padding: 1px" class="table table-condensed table-striped table-hover table-bordered" id="data_table">
							<thead>
								<tr>
									<th style="width:20%; white-space: nowrap; text-align: center; padding-left: 1px; padding-right: 1px" >
										구분
									</th>
									<th style="white-space: nowrap; text-align: center; padding-left: 1px; padding-right: 1px">
										총계
									</th>
									@foreach ($types as $t)
									<th style="white-space: nowrap; text-align: center; padding-left: 1px; padding-right: 1px">
										{{ $t->type_name }}
									</th>
									@endforeach
								</tr>
							</thead>
							<tbody>

							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@stop

@section('scripts')
{{ HTML::script('static/vendor/fancybox/jquery.mousewheel-3.0.6.pack.js') }}
{{ HTML::script('static/vendor/fancybox/jquery.fancybox.js?v=2.1.5.js') }}
{{ HTML::script('static/vendor/fancybox/helpers/jquery.fancybox-buttons.js?v=1.0.5') }}
{{ HTML::script('static/vendor/fancybox/helpers/jquery.fancybox-thumbs.js?v=1.0.7') }}
{{ HTML::dataTables() }}
<script type="text/javascript">
var dataTable;

$(function() {

	$(".positive-int").on('change', function(){
		var input = $(this).val();
		var re = /^\d+$/;

		if (!re.test(input)) {
		alert('양의 정수만 입력하세요');
		$(this).val(0);
		};
	});
	$(".positive").on('change', function(){
		var input = $(this).val();
		var re = /([0-9]+\.[0-9]*)|([0-9]*\.[0-9]+)|([0-9]+)/;

		if (!re.test(input)) {
		alert('양수만 입력하세요');
		$(this).val(0);
		};
	});

	$("#count_update_btn").click(function() {
		$("#count_update_form").submit();
	});
	$("#wrecked_update_btn").click(function() {
		$("#wrecked_update_form").submit();
	});
	$("#discard_register_btn").click(function(){
		popup(base_url+'/equips/items/{{$item->id}}/discard',1280,900);
	});
	$("#discard_history_btn").click(function(){
		popup(base_url+'/equips/items/{{$item->id}}/discard_list',1280,900);
	});


	$(".detail-title").click(function() {
		var detailId = $(this).data('id');
		var itemId = $(this).data('item-id');
		popup(base_url+'/equips/items/'+itemId+"/detail/"+detailId, 800, 900);
	});

	$("#data_view").click(function() {
		loadData(null);
	});

	$(".fancybox").fancybox({
		openEffect	: 'none',
		closeEffect	: 'none'
	});
	$("#item_detail_btn").click(function() {
		var id = $(this).attr('data-item-id');
		popup(base_url+'/equips/items/'+id+'/details', 800, 900);
	});

	$("#delete_btn").click(function() {
		if (!confirm('일괄 폐기하시겠습니까?')) {
			return;
		}
		var id = $("#item_id").val();

		$.ajax({
			url: url("admin/item/"+id),
			type: "delete",
			success: function(res){
				alert(res.message);
				if (res.result == 0) {
					redirect('equips/inventories');
				}
			}
		});
	});

	dataTable = $("#data_table").DataTable({
		autoWidth: true,
		processing: true,
		searching: false,
		ordering: false,
		lengthChange: false,
		paging: false,
		ajax: url("equips/items/{{$item->id}}/data"),
		columns: [
			{
				data: "node.node_name",
				render: function (data, type, row, meta) {
					if (type != 'display') {
						return data;
					}
					if (row.node.is_terminal == 1) {
						return data;
					} else {
						return '<a href="#data_table" onclick="loadData('+row.node.id+')">'+data+'</a>'
					}
				}
			},
			{ data: "sum_row" },
			@foreach ($types as $t)
				{{ '{ data: "'.$t->type_name.'" },'}}
			@endforeach
		]
	});
});
function loadData(parentId) {

	parentId = parentId || "";

	dataTable.ajax.url(url("equips/items/{{$item->id}}/data?parent="+parentId)).load();
}
</script>
@stop

@section('styles')
{{ HTML::style('static/vendor/fancybox/jquery.fancybox.css?v=2.1.5') }}
{{ HTML::style('static/vendor/fancybox/helpers/jquery.fancybox-buttons.css?v=1.0.5"') }}
{{ HTML::style('static/vendor/fancybox/helpers/jquery.fancybox-thumbs.css?v=1.0.7"') }}
{{ HTML::style('static/css/eq.css') }}
@stop
