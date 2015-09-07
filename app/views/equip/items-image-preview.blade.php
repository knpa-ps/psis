<div class="alert alert-success">
	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
	<img src="{{ url('/uploads/'.$img->url)}}" width="80" alt="">
	<input type="hidden" value="{{ url('/uploads/'.$img->url)}}" class="item-images">
</div>