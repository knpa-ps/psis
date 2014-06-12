@extends('layouts.base')
@section('styles')
<style>
body {
	background : #fff;
}	
</style>

@stop
@section('body')
<div class="col-xs-12">
	<h3><b>{{ EqItem::find($itemId)->name}} 장비상세정보 - 기타정보</b></h3>
	<div class="btn-group pull-right" style="margin-bottom: 20px;">
		<a href="{{URL::to('/equips/items/'.$itemId.'/new_detail')}}" class="btn btn-primary btn-xs">
			<span class="glyphicon glyphicon-pencil"> 글쓰기</span>
		</a>
		<div class="clearfix"></div>
	</div>
	<table class="table table-condensed table-hover table-striped table-bordered" id="details_table">
		<colgroup>
			<col class="col-xs-8">
			<col class="col-xs-2">
			<col class="col-xs-2">
		</colgroup>
		<thead>
			<tr>
				<th>제목</th>
				<th>작성자</th>
				<th>날짜</th>
			</tr>
		</thead>
		<tbody>
			@foreach ($details as $d)
				<tr>
					<td><a href="{{URL::to('/equips/items/'.$itemId.'/detail/'.$d->id)}}">{{$d->title}}</a></td>
					<td>{{ $d->creator->user_name }}</td>
					<td>{{$d->created_at->format('Y-m-d')}}</td>
				</tr>
			@endforeach
		</tbody>
	</table>
</div>
@stop