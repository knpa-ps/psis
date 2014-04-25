<div class="panel panel-default">
	<div class="panel-body">
		<div class="row">
			<div class="col-xs-12">
				<div class="page-header" id="report_title">
					<h3><small>제목</small> <strong>{{ $report->title }}</strong></h3>
					<h3>
						<div class="row">
							<div class="col-xs-4">
								<small>작성처</small> {{ $report->department->full_name }} 
							</div>
							<div class="col-xs-4">
								<small>작성자</small> {{ $report->user->user_name }}
							</div>
							<div class="col-xs-4">
								<small>작성시간</small> {{ $report->created_at->format('Y-m-d h:i') }}
							</div>
						</div>
					</h3>
					<input type="hidden" id="current_report_id" value="{{ $report->id }}">
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-10">
				<div style="width:100%; height:800px; background:green">
					
				</div>
			</div>
			<div class="col-xs-2">
				<h5>작업</h5>
				<div class="btn-group-vertical report-toolbar">
					<a href="{{ url('reports/create?rid='.$report->id) }}" class="btn btn-primary btn-block">
						<small><span class="glyphicon glyphicon-share"></span> 추가속보작성</small>
					</a>
				</div>
				<div class="btn-group-vertical report-toolbar">
					
					<button type="button" class="btn btn-default btn-block" id="report_export">
						<small><span class="glyphicon glyphicon-download-alt"></span> 다운로드</small>
					</button>
					
					@if ($permissions['update'])
						<a href="{{ url('reports/edit?rid='.$report->id) }}" class="btn btn-default btn-block">
							<small><span class="glyphicon glyphicon-edit"></span> 수정</small>
						</a>
					@endif

					@if ($permissions['delete'])
						<button type="button" class="btn btn-default btn-block" id="report_delete">
							<small><span class="glyphicon glyphicon-trash"></span> 삭제</small>
						</button>
					@endif
				</div>
			</div>
		</div>
	</div>
</div>

@section('styles')
<style type="text/css" media="screen">
	#report_title {
		margin-top: 0;
	}
	.report-toolbar {
		width: 100%;
		margin-bottom: 10px;
	}
</style>
@stop

@section('scripts')
<script type="text/javascript">
$(function() {
	$("#repot_export").click(function() {

	});

	$("#report_delete").click(function(){
		if (!confirm('정말 삭제하시겠습니까?')) {
			return;
		}

		var report_id = $("#current_report_id").val();

		$.ajax({
			url: base_url+"/reports/delete",
			type: "post",
			data: { rid:report_id },
			success: function(response) {
				alert(response.message);
				if (response.result == 0) {
					redirect(base_url+"/reports/list");
				}
			}
		});
	});
});
</script>
@stop