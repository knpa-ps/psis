@extends('layouts.master')

@section('content')
<div class="row">
	<div class="col-xs-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">
					<strong>장비상세정보</strong>
				</h3>
			</div>

			<div class="panel-body">
				{{-- 기본정보 --}}
				<div class="row">
					<div class="col-xs-6">
						<h4 class="block-header"><small>{{ $item->category->name }}</small> {{ $item->name }}</h4>
						<input type="hidden" id="item_id" value="{{ $item->id }}">
					</div>
					
					<div class="col-xs-6">
						<a href="{{url('equips/items/'.$item->id.'/edit')}}" class="btn btn-xs btn-success pull-right">
							<span class="glyphicon glyphicon-edit"></span> 수정
						</a>
						<a href="#" class="btn btn-xs btn-danger pull-right" id="delete_btn"> 
							<span class="glyphicon glyphicon-trash"></span> 삭제
						</a>
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
										<img src="{{ url('/static/img/no_image_available_big.png') }}" alt="" />
									</div>
								@else
									@foreach ($item->images as $idx=>$i)
									
									<div class="item {{ $idx==0?'active':'' }}">
										<a class="fancybox" rel="gallery" href="{{ $i->url }}">
											<img src="{{ $i->url }}" alt="" />
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
									<td colspan="5"><strong> {{ $item->name }} </strong></td>
								</tr>
								<tr>
									<th>기능</th>
									<td> {{ $item->category->domain->name }} </td>
									<th>분류</th>
									<td colspan="3"> {{ $item->category->name }} </td>
								</tr>
								<tr>
									<th>제원</th>
									<td> {{$item->standard}} </td>
									<th>단위</th>
									<td>{{ $item->unit }}</td>
									<th>내구연한</th>
									<td> {{ $item->persist_years }} </td>
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
									<td> {{$detail->title}} </td>
									<td> {{$detail->created_at->format('Y-m-d')}} </td>
								</tr>
								@endforeach
							</tbody>
						</table>
						{{-- 기타정보 목록 끝 --}}
					</div>
				</div>{{-- 기본정보 끝 --}}

				{{-- 보유내역 --}}
				<div class="row">
					<div class="col-xs-12">
						<ul class="nav nav-tabs">
							
							<li class="active">
								<a href="#inv_dept">
									관서별 보유현황
								</a>
							</li>

							<li>
								<a href="#inv_region">
									지방청별 보유현황
								</a>
							</li>

						</ul>


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
<script type="text/javascript">
$(function() {
	$(".fancybox").fancybox({
		openEffect	: 'none',
		closeEffect	: 'none'
	});
	$("#item_detail_btn").click(function() {
		var id = $(this).data('id');
		popup("equips/"+id+"/details", 800, 900);
	});
	$("#delete_btn").click(function() {
		if (!confirm('삭제하시겠습니까?')) {
			return;
		}

		var id = $("#item_id").val();

		$.ajax({
			url: url("equips/items/"+id),
			type: "delete",
			success: function(res){ 
				alert(res.message);
				if (res.result == 0) {
					redirect('equips/items');
				}
			}
		});
	});
});
</script>
@stop

@section('styles')

{{ HTML::style('static/vendor/fancybox/jquery.fancybox.css?v=2.1.5') }}
{{ HTML::style('static/vendor/fancybox/helpers/jquery.fancybox-buttons.css?v=1.0.5"') }}
{{ HTML::style('static/vendor/fancybox/helpers/jquery.fancybox-thumbs.css?v=1.0.7"') }}
{{ HTML::style('static/css/eq.css') }}
@stop