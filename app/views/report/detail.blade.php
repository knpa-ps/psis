@extends('layouts.master')

@section('content')
<div class="row-fluid">
	<div class="box span12">
		<div class="box-header well">
			<h2>
				<i class="icon-th-list"></i>
				경비상황보고서
			</h2>
		</div>
		<div class="box-content">
			<div class="row-fluid">
				<div class="span12">
					@if ($user->hasAccess('reports.update'))
					<button class="btn btn-primary {{ $report->closed?"disabled":"" }}" id="edit_report">
						<i class="icon-edit icon-white"></i> 변경내역제출 
					</button>
					@endif
					@if ($user->hasAccess('reports.close'))
					<button class="btn {{ $report->closed?"active":"" }}" id="set_closed">
						<i class="icon-lock"></i> 마감
					</button>
					@endif
					<div class="btn-group">
						<button class="btn dropdown-toggle" data-toggle="dropdown" type="button">
							<i class="icon-th-list"></i> 변경내역조회 {{ '('.count($histories).')' }} 
							<span class="caret"></span>
						</button>
						<ul class="dropdown-menu">
							@foreach ($histories as $h)
							@if ($h->id != $reportData->id)
								<li>
							@else
								<li class="active">
							@endif
									<a href="{{ action('ReportController@showReport') }}?id={{ $report->id }}&hid={{ $h->id }}">
										{{ $h->created_at }} {{ $h->user['user_name'] }}
									</a>
								</li>
							@endforeach
						</ul>
					</div>
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
				<div class="page-header">
					<h2>
						{{ $report->title }}
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
			
			<OBJECT id="HwpCtrl" height="1200" width="100%" align="center" 
							classid="CLSID:BD9C32DE-3155-4691-8972-097D53B10052">

                <param name="TOOLBAR_MENU" value="true">
                <param name="TOOLBAR_STANDARD" value="true">
                <param name="TOOLBAR_FORMAT" value="true">
                <param name="TOOLBAR_DRAW" value="true">
                <param name="TOOLBAR_TABLE" value="true">
                <param name="TOOLBAR_IMAGE" value="true">
                <param name="TOOLBAR_HEADERFOOTER" value="false">

                <param name="SHOW_TOOLBAR" value="{{$report->closed?'false':'true'}}">
                <div class="alert" >
                	HwpCtrl이 설치되지 않아서 보고서를 조회할 수 없습니다.
                </div>
			</OBJECT>

		</div>
	</div>
</div>
<div class="hide" id="hwp_content">
{{$reportData->content}}
</div>
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
