@extends('layouts.base')
@section('styles')
<style>
body {
	background : #fff;
}	
img {
	max-width: 100%;
	height: auto;
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
	<h2>{{ $detail->title }}</h2>
	<span>작성자 <b>{{$detail->creator->user_name}}</b>	{{$detail->created_at}}<w/span>
	<hr>
	<div class="col-xs-12">
		<div class="row">
			<div class="btn-group pull-right">
				<a href="{{url('/equips/items/'.$itemId.'/detail/'.$id.'/update')}}" class="btn btn-xs btn-default" style="margin-right: 8px;">
					<span class="glyphicon glyphicon-pencil"> 수정</span>
				</a>
				<button class="delete btn btn-xs btn-default" style="margin-right: 8px;">
					<span class="glyphicon glyphicon-remove"> 삭제</span>
				</button>
				<a href="{{URL::to('/equips/items/'.$itemId.'/details')}}" class="btn btn-xs btn-default">
					<span class="glyphicon glyphicon-list"> 목록</span>
				</a>
			</div>	
		</div>
		<div class="row">
			<div >
				<p>{{ $detail->content }}</p>
			</div>
		</div>
		<hr>
		<div class="row">
			<div class="btn-group pull-right" style="margin-bottom: 10px;">
				<a href="{{url('/equips/items/'.$itemId.'/detail/'.$id.'/update')}}" class="btn btn-xs btn-default" style="margin-right: 8px;">
					<span class="glyphicon glyphicon-pencil"> 수정</span>
				</a>
				<button class="delete btn btn-xs btn-default" style="margin-right: 8px;">
					<span class="glyphicon glyphicon-remove"> 삭제</span>
				</button>
				<a href="{{URL::to('/equips/items/'.$itemId.'/details')}}" class="btn btn-xs btn-default">
					<span class="glyphicon glyphicon-list"> 목록</span>
				</a>
			</div>	
		</div>
	</div>

</div>
@stop
@section('scripts')
<script type="text/javascript">
$(function(){
	$('.delete').on('click', function(){
		if(!confirm("정말 삭제하시겠습니까?")){
			return;
		}
		$.ajax({
			url : base_url+"/equips/items/"+{{ $itemId }}+"/detail/"+{{$id}},
			type : "DELETE",
			success: function(response){
				alert(response.message);
				if (response.result==0) {
					redirect(response.url);
				}
			}
		});
	});
});
</script>
@stop