<div class="alert alert-success">
	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
	@if(isset($img))
		<!-- 장비수정 할때 ($img값이 있음) -->
		<img src="{{ url('/uploads/'.$img->url)}}" width="80" alt="">
		<input type="hidden" value="{{$img->url}}" class="item-images">
	@else
		<!-- 장비추가 할때 ($img값이 없음) -->
		<img src="" width="80" alt="">
		<input type="hidden" value="" class="item-images">
	@endif
</div>
