@extends('layouts.base')
@section('styles')
<style>
body {
	background : #fff;
}	
</style>
{{ HTML::style('static/vendor/uploadify/uploadify.css') }}
@stop
@section('body')
<div class="col-xs-12">
	<h3><b>새 글 작성</b></h3>
	<div class="col-xs-12">
		<div class="row">
			<form action="{{url('/equips/items/'.$itemId.'/new_detail')}}" method="post" name="new_detail_form" id="new_detail_form" role="form" class="form-horizontal" novalidate>

				<table class="table table-striped">
					<colgroup>
						<col class="col-xs-2">
						<col class="col-xs-10">
					</colgroup>
					<tr>
						<th>작성자</th>
						<td>{{$user->user_name}}</td>
					</tr>
					<tr>
						<th>제목</th>
						<td><input class="form-control" type="text" name="title"></td>
					</tr>
					<tr>
						<th>내용</th>
						<td>
							<textarea name="input_body" id="input_body" cols="80" rows="10"></textarea>
						</td>
					</tr>
					<tr>
						<th>파일 첨부</th>
						<td><input type="file" name="file_upload" id="file_upload" /></td><br>
					</tr>
					<input type="hidden" name="files" id="files" value="" />
				</table>
				<div class="text-center">
					<div class="btn-group">
						<button type="submit" class="btn btn-primary btn-xs">작성완료</button>
						<a href="{{url('/equips/items/'.$itemId.'/details')}}" class="btn btn-default btn-xs">취소</a>
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
{{ HTML::script('static/vendor/uploadify/jquery.uploadify.min.js')}}
<script>
CKEDITOR.replace( 'input_body', {
	filebrowserUploadUrl : "{{url('/upload/image/ckeditor')}}"
});
$(function() {
	var attachedFiles = [];
	$('#file_upload').uploadify({
        'swf'      : url('static/vendor/uploadify/uploadify.swf'),
        'uploader' : url('static/vendor/uploadify/uploadify.php'),
        'onUploadSuccess' : function(file, data, response)  {
        	attachedFiles.push(data);
        	$("#files").val(JSON.stringify(attachedFiles));
        },
        removeCompleted:false
    });

	$("#new_detail_form").validate({
		rules: {
			title : {
				required: true
			}
		}
	});
});
</script>
@stop