@extends('layouts.base')

@section('body')

<div style="width: 90%; margin: auto; ">
<div class="row panel panel-default">
	<div class="col-xs-12 panel-body">
		<div class="page-title">
			<h4><strong>임시저장속보 불러오기</strong></h4>
		</div>
		<table class="table table-condensed table-bordered table-striped table-hover" id="drafts_table">
			<thead>
				<tr>
					<th>제목</th>
					<th>저장일시</th>
					<th>작업</th>
				</tr>
			</thead>
			<tbody>
				@foreach ($drafts as $d)
					<tr data-id="{{$d->id}}">
						<td>{{ str_limit($d->title, 21) }}</td>
						<td>{{ $d->created_at->format('Y-m-d h:i') }}</td>
						<td>
							<button class="btn btn-default btn-xs btn-primary select-draft" type="button">
								<span class="glyphicon glyphicon-ok"></span> 선택
							</button>
							<button class="btn btn-default btn-xs btn-danger remove-draft" type="button">
								<span class="glyphicon glyphicon-remove"></span> 삭제
							</button>
						</td>
					</tr>
				@endforeach
			</tbody>
		</table>

		{{ $drafts->links() }}
	</div>
</div>
</div>

@stop

@section('scripts')
<script type="text/javascript">
$(function() {
	$(".select-draft").click(function() {
		var id = $(this).parent().parent().data('id');

		$.ajax({
			url: base_url+"/eventreports/drafts/"+id,
			dataType: 'json',
			beforeSend: function() {
				$('body').modalmanager('loading');
			},
			success: function(response) {
				window.opener.onDraftSelected(response);
				window.close();
			},
			complete: function() {
				$('body').modalmanager('loading');
			}
		});
	});

	$(".remove-draft").click(function() {
		if (!confirm('삭제하시겠습니까?')) {
			return;
		}

		var id = $(this).parent().parent().data('id');

		$.ajax({
			url: base_url+"/eventreports/drafts/delete",
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
