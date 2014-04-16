@extends('layouts.master')

@section('content')
<div class="row-fluid">
	<div class="span12 well well-small">
		<div class="header">
			<h4>옵션</h4>
		</div>

		<a class="btn btn-primary" id="copy_report" href="{{ action('ReportController@copyReport') }}?id={{ $report->id }}&hid={{ $reportData->id }}">
			<i class="icon-file"></i>
			새로작성
		</a>
		<div class="btn-group">
			<button class="btn dropdown-toggle" data-toggle="dropdown" type="button">
				<i class="icon-file"></i> 첨부파일 {{ '('.count($files).')' }}
				 <span class="caret"></span>
			</button>
			<ul class="dropdown-menu">
			@foreach ($files as $f)
				<li>
					<a href="{{ action('FileController@download') }}?id={{$f->id}}">
						{{ $f->name }} [{{ round($f->size/1024) }} KB]
					</a>
				</li>
			@endforeach
			</ul>
		</div>
	</div>
</div>
<div class="row-fluid">
	<div class="panel panel-default span12">
		<div class="panel-body">

			<div class="row-fluid">
				<div class="page-header">
					<h2> {{ $report->title }}
					</h2>
				</div>
			</div>
			<div class="row-fluid">
				<div class="span4">
					<dl class=" dl-horizontal">
						<dt>작성처</dt>
						<dd>{{ $report->user->department?$report->user->department->parseFullName():'' }}</dd>
						<dt>작성자</dt>
						<dd>{{ $report->user->user_name }}</dd>
					</dl>
				</div>
				<div class="span4">
					<dl class="dl-horizontal">
						<dt>작성일시</dt>
						<dd>{{ $report->created_at }}</dd>
						<dt>상태</dt>
						<dd>{{ $report->closed?"마감":"수신" }}</dd>
					</dl>
				</div>
				<div class="span4">
					
				</div>
			</div>
			
        
			<div class="row-fluid">
				<div class="span12" style="text-align: center;  " align="center">
					<OBJECT id="HwpCtrl" height="800" width="700" align="center" 
							classid="CLSID:BD9C32DE-3155-4691-8972-097D53B10052">

		                <param name="TOOLBAR_MENU" value="false">
		                <param name="TOOLBAR_STANDARD" value="false">
		                <param name="TOOLBAR_FORMAT" value="false">
		                <param name="TOOLBAR_DRAW" value="false">
		                <param name="TOOLBAR_TABLE" value="false">
		                <param name="TOOLBAR_IMAGE" value="false">
		                <param name="TOOLBAR_HEADERFOOTER" value="false">

		                <param name="SHOW_TOOLBAR" value="false">
		                <div class="alert" >
		                	HwpCtrl이 설치되지 않아서 보고서를 조회할 수 없습니다.
		                </div>
					</OBJECT>
				</div>
			</div>

		</div>
	</div>
</div>
<div class="hide" id="hwp_content">
{{$reportData->content}}
</div>
@stop

@section('styles')
<style type="text/css" media="screen">
#copy_report a {
	text-decoration: none;
	color:black;
}	
</style>
@stop

@section('scripts')
<script  type="text/javascript" >
var HwpCtrl;
var hasHwpCtrl;
$(function(){

    HwpCtrl = document.getElementById("HwpCtrl");
    if (typeof HwpCtrl.open === 'undefined') {
        bootbox.alert("@lang('strings.no_hwpctrl')"
        );
        hasHwpCtrl = false;
    } else {
    	hasHwpCtrl = true;
        var data = $("#hwp_content").html();
    	HwpCtrl.SetTextFile(data, "HWP", "");
    }

	$("#set_closed").click(function(){
		var b = !$(this).hasClass('active');
		var msg = b?"마감하시겠습니까?":"마감을 해제하시겠습니까?";
		bootbox.confirm(msg, function(result){
			if (!result) {
				return;
			}
			$.ajax({
				url: "{{ action('ReportController@setClosed') }}",
				type: "post",
				data: JSON.stringify({
					"closed":b,
					"ids": [ {{$report->id}} ]
				}),
				contentType: "application/json; charset=utf-8",
				success: function(data) {
					if (data) {
						noty({type:"error", layout:"topRight", text:data});
					} else {
						noty({type:"success", layout:"topRight", text:"상태가 변경되었습니다."});
						$("#set_closed").toggleClass('active');
						$("#edit_report").toggleClass('disabled');
					}
					window.location.reload();
				}
			});
		});
	});

	$("#edit_report").click(function(){
		if ($("#set_closed").hasClass('active')) {
			return;
		}

		bootbox.confirm("수정된 내역을 반영하시겠습니까?", function(result){
			if (!result) {
				return;
			}

			if (!hasHwpCtrl) {
				return;
			}

			var content = HwpCtrl.GetTextFile("HWP", "");
			var params = [
				@foreach ($files as $f)
				{"name": "files[]", "value": "{{ $f->id }}" },
				@endforeach
				
				{"name": "content", "value": content},
				{"name": "rid", "value": {{ $report->id }} }
			];

			$.ajax({
				url: "{{ action('ReportController@editReport') }}",
				type: "post",
				data: params,
				success: function(hid) {
				window.location = "{{ action('ReportController@showReport') }}?id={{ $report->id }}&hid="+hid;
				}
			});
		});

	});
});
</script>
@stop
