@extends('layouts.base')

@section('body')

<div style="width: 90%; margin: auto; ">
<div class="row panel panel-default">
	<div class="col-xs-12 panel-body">
		<div class="page-title">
			<h4><strong>속보양식선택</strong></h4>
		</div>
		<table class="table table-condensed table-bordered table-striped table-hover" id="templates_table">
			<thead>
				<tr>
					<th>이름</th>
					<th>작업</th>
				</tr>
			</thead>
			<tbody>
				@foreach ($templates as $t)
					<tr data-id="{{ $t->id }}">
						<td>{{$t->name}}</td>
						<td>

							<button class="btn btn-default btn-xs btn-primary select-template" type="button">
								<span class="glyphicon glyphicon-ok"></span> 선택
							</button>
							@if (Sentry::getUser()->isSuperUser() or Sentry::getUser()->hasAccess('addReportForm'))
							<button class="btn btn-default btn-xs btn-danger remove-template" type="button">
								<span class="glyphicon glyphicon-remove"></span> 삭제
							</button>
							@if ($t->is_default==0)
							<button class="btn btn-default btn-xs btn-success set-default" type="button">
								<span class="glyphicon glyphicon-check"></span> 기본값
							</button>
							@endif
							@endif
						</td>
					</tr>
				@endforeach
			</tbody>
		</table>
	</div>
</div>
</div>

@stop

@section('scripts')
<script type="text/javascript">
$(function() {
	$(".select-template").click(function() {
		var id = $(this).parent().parent().data('id');

		$.ajax({
			url: base_url+"/eventreports/templates/"+id,
			dataType: 'json',
			beforeSend: function() {
				$('body').modalmanager('loading');
			},
			success: function(response) {
				window.opener.onTemplateSelected(response, id);
				window.close();
			},
			complete: function() {
				$('body').modalmanager('loading');
			}
		});
	});

	$(".set-default").click(function(){
		if (!confirm('이 양식을 기본값으로 설정합니까?')) {
			return;
		}

		var id = $(this).parent().parent().data('id');

		$.ajax({
			url: base_url+"/eventreports/templates/set_default",
			type: "post",
			data: { id: id },
			dataType: 'json',
			beforeSend: function() {
				$('body').modalmanager('loading');
			},
			success: function() {
				alert("기본값으로 지정되었습니다.");
				window.close();
			}
		});
	})
	$(".remove-template").click(function() {
		if (!confirm('삭제하시겠습니까?')) {
			return;
		}

		var id = $(this).parent().parent().data('id');

		$.ajax({
			url: base_url+"/eventreports/templates/delete",
			type: "post",
			data: { id: id },
			dataType: 'json',
			beforeSend: function() {
				$('body').modalmanager('loading');
			},
			success: function() {
				window.location.reload();
			},
			complete: function() {
				$('body').modalmanager('loading');
			}
		});
	});
});
</script>
@stop
