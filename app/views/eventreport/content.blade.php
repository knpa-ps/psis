<div class="panel panel-default">
	<div class="panel-body">
		<div class="row">
			<div class="col-xs-12">
				<div class="page-header" id="report_title">
					<h3><small>제목 : </small> <strong>{{ $report->title }}</strong></h3>
					<h3><small>유형 : </small> <strong>{{ $report->reportType->name }}</strong></h3>
					<h3>
						<div class="row">
							<div class="col-xs-4">
								<small>작성처 : </small> {{ $report->user->department->full_name }}
							</div>
							<div class="col-xs-4">
								<small>작성자 : </small> {{ $report->user->user_name }}
							</div>
  							<div class="col-xs-4">
								<small>작성시간 : </small> {{ $report->created_at->format('Y-m-d H:i') }}
							</div>
						</div>
					</h3>
					<input type="hidden" id="current_report_id" value="{{ $report->id }}">
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-10">
				<input type="hidden" id="hwpctrl_content" value="{{ $report->histories()->lastest()->first()->content }}">
				<object id="HwpCtrl" height="800" width="100%" align="center"
						classid="CLSID:BD9C32DE-3155-4691-8972-097D53B10052">
	                <param name="TOOLBAR_MENU" value="false">
	                <param name="TOOLBAR_STANDARD" value="true">
	                <param name="TOOLBAR_FORMAT" value="false">
	                <param name="TOOLBAR_DRAW" value="false">
	                <param name="TOOLBAR_TABLE" value="false">
	                <param name="TOOLBAR_IMAGE" value="false">
	                <param name="TOOLBAR_HEADERFOOTER" value="false">
	                <param name="SHOW_TOOLBAR" value="true">

	                <div class="alert alert-warning">
	                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
	                    <strong>ActiveX 미설치</strong> HwpCtrl이 설치되지 않아서 보고서를 조회할 수 없습니다.
	                </div>
  				</object>
  			</div>
			<div class="col-xs-2">
				<h5>작업</h5>
				<div class="btn-group-vertical report-toolbar">
					<a href="{{ url('eventreports/create?rid='.$report->id) }}" class="btn btn-primary btn-block">
						<small><span class="glyphicon glyphicon-share"></span> 추가속보작성</small>
					</a>
				</div>
				<div class="btn-group-vertical report-toolbar">

					@if ($permissions['update'])
						<a href="{{ url('eventreports/edit?rid='.$report->id) }}" class="btn btn-default btn-block">
							<small><span class="glyphicon glyphicon-edit"></span> 수정</small>
						</a>
					@endif

					@if ($permissions['delete'])
						<button type="button" class="btn btn-default btn-block" id="report_delete">
							<small><span class="glyphicon glyphicon-trash"></span> 삭제</small>
						</button>
					@endif
				</div>
				<div class="btn-group-vertical report-toolbar">

					@if ($next_id)
						<a href="{{ url('eventreports/list?'.http_build_query(array_merge($input, array('rid'=>$next_id, 'page'=>Input::get('page'))))) }}" type="button" class="btn btn-default">
					@else
						<a href="#" type="button" class="btn disabled btn-default">
					@endif

			    	<small><span class="glyphicon glyphicon-chevron-up"></span> 다음글</small></a>

					@if ($prev_id)
						<a href="{{ url('eventreports/list?'.http_build_query(array_merge($input, array('rid'=>$prev_id, 'page'=>Input::get('page'))))) }}" type="button" class="btn btn-default">
					@else
						<a href="#" type="button" class="btn disabled btn-default">
					@endif

			  		<small><span class="glyphicon glyphicon-chevron-down"></span> 이전글</small></a>

				</div>
			</div>
		</div>
	</div>
</div>

@section('styles')
@parent
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
@parent
{{ HTML::script('static/js/hwpctrl.js') }}
<script type="text/javascript">

$(function() {
  if (initHwpCtrl()) {
    var data = $("#hwpctrl_content").val();
  	vHwpCtrl.SetTextFile(data, "HWP", "속보를 불러올 수 없습니다.");
    vHwpCtrl.EditMode = 0;
		vHwpCtrl.SetToolBar(3, "FileSaveAs");
		vHwpCtrl.SetToolBar(3, 1);
  }

	$("#report_delete").click(function(){
		if (!confirm('정말 삭제하시겠습니까?')) {
			return;
		}

		var report_id = $("#current_report_id").val();

		$.ajax({
			url: base_url+"/eventreports/delete",
			type: "post",
			data: { rid:report_id },
			success: function(response) {
				alert(response.message);
				if (response.result == 0) {
					redirect(base_url+"/eventreports/list");
				}
			}
		});
	});
});
</script>
@stop
