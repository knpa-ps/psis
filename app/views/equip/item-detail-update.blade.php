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
	<h3><b>수정하기</b></h3>
	<div class="col-xs-12">
		<div class="row">
			<form action="{{url('/equips/items/'.$itemId.'/detail/'.$id.'/update')}}" method="post" name="update_detail_form" id="update_detail_form" role="form" class="form-horizontal" novalidate>
				<table class="table table-striped">
					<colgroup>
						<col class="col-xs-2">
						<col class="col-xs-10">
					</colgroup>
					<tr>
						<th>작성자</th>
						<td>{{$creator_name}}</td>
					</tr>
					<tr>
						<th>제목</th>
						<td><input class="form-control" type="text" name="title" value='{{$title}}'></td>
					</tr>
					<tr>
						<th>내용</th>
						<td>
							<textarea name="input_body" id="input_body" cols="80" rows="10">{{$content}}</textarea>
						</td>
					</tr>
				</table>
				<div class="text-center">
					<div class="btn-group">
						<button type="submit" class="btn btn-primary btn-xs">저장</button>
						<a href="{{url('/equips/items/'.$itemId.'/detail/'.$id)}}" class="btn btn-default btn-xs">취소</a>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
@stop
@section('scripts')
{{ HTML::script('static/vendor/ckeditor/ckeditor.js') }}
{{ HTML::script('static/vendor/validate/jquery.validate.min.js') }}
<script>
CKEDITOR.replace( 'input_body', {
	filebrowserUploadUrl : "{{url('/upload/image/ckeditor')}}"
});
$(function() {
	$("#update_detail_form").validate({
		rules: {
			title : {
				required: true
			}
		}
	});
})
</script>
@stop