@extends('layouts.master')

@section('content')
<div class="row-fluid">		
	<div class="box span12">
		<div class="box-header well" data-original-title>
			<h2><i class="icon-edit"></i> @lang('strings.report_compose')</h2>
			<div class="box-icon">
			</div>
		</div>
		<div class="box-content">

			<div class="controls">
				<div class="btn-group" id="select_template">
					<a class="btn dropdown-toggle" data-toggle="dropdown" href="#">보고서 양식 <span class="caret"></span></a>
					<ul class="dropdown-menu">
						<li><a href="#" data-value="1" >양식1(테두리 있음)</a></li>
						<li><a href="#" data-value="2">양식2(테두리 없음)</a></li>
					</ul>
				</div>
			</div>

			<form class="form-horizontal" >
				
				<fieldset>
					<div class="control-group">
						<label for="report-title" class="control-label">
							@lang('strings.report_title')
						</label>
						<div class="controls">
							<input type="text" class="span12" name="report-title" id="report-title">
						</div>
					</div>

					<div class="control-group">
						<label for="report-content" class="control-label">
							@lang('strings.report_content')
						</label>
						<div class="controls">
							<OBJECT id="HwpCtrl" height="1200" width="100%" align="center" 
							classid="CLSID:BD9C32DE-3155-4691-8972-097D53B10052">
                                <param name="TOOLBAR_MENU" value="true">
                                <param name="TOOLBAR_STANDARD" value="true">
                                <param name="TOOLBAR_FORMAT" value="true">
                                <param name="TOOLBAR_DRAW" value="true">
                                <param name="TOOLBAR_TABLE" value="true">
                                <param name="TOOLBAR_IMAGE" value="true">
                                <param name="TOOLBAR_HEADERFOOTER" value="false">
                                <param name="SHOW_TOOLBAR" value="true">

				                <div class="alert" >
				                	HwpCtrl이 설치되지 않아서 보고서를 작성할 수 없습니다.
				                </div>
							</OBJECT>
							
						</div>
					</div>
					<div class="control-group">
						<label for="report-attach" class="control-label">
							@lang('strings.report_attachments')
						</label>
						<div class="controls">
						    <input data-no-uniform="true" type="file" name="file_upload" id="file_upload" />
						</div>
					</div>
					
					<div class="form-actions">
						<button type="button" class="btn btn-primary" id="report-submit">
							@lang('strings.compose')
						</button>
					</div>
				</fieldset>
								
			</form>
		
		</div>
	</div>
</div>

<form>
</form>

<div class="modal hide" id="upload-modal" data-backdrop="false">
	<div class="modal-body">
		<p>@lang('strings.report_uploading')</p>
		<div class="progress progress-striped active">
			<div class="bar" style="width:100%;"></div>
		</div>
	</div>
</div>

@stop

@section('styles')
{{HTML::style('static/css/uploadify.css')}}
@stop

@section('scripts')
{{HTML::script('static/js/jquery.uploadify-3.1.min.js')}}
<script language="JavaScript" type="text/javascript">
var HwpCtrl;
var hasHwpCtrl = false;
function getActiveX(progId) {
	try {
		var obj = new ActiveXObject(progId);
		if (obj) return obj;
		else return null;
	}
	catch(e) {
		return null;
	}
}
function OnStart()
{
    HwpCtrl = document.getElementById("HwpCtrl");
    if (typeof HwpCtrl.open === 'undefined') {
        bootbox.alert("@lang('strings.no_hwpctrl')"
        );
        hasHwpCtrl = false;
        return;
    } else {
    	hasHwpCtrl = true;
    }
	HwpCtrl.open("{{ url('static/misc/report1.hwp') }}");
}
var attachments = [];
$(function(){
	$('#file_upload').uploadify({
		'auto' : false,
		'fileTypeExts' : '*.gif; *.jpg; *.png; *.hwp; *.xls; *.xlsx; *.cell;',
		'swf'      : "{{ url('static/misc/uploadify.swf') }}",
		'uploader' : "{{ action('ReportController@uploadAttachments') }}" ,
		onUploadSuccess : function(file, data, response) {
			attachments.push({"name":"files[]", "value":data});
		},
		onQueueComplete: function(queueData) {
			if (queueData.uploadsErrored == 0) {
				postReport();
			} else {
				bootbox.alert("@lang('strings.failed_to_upload')"
					);
			}
		}
	});

	OnStart();
    
    $("#report-submit").click(function(){

    	if (!$.trim($("#report-title").val()).length) {
    		bootbox.alert('@lang('strings.need_title')'
    			);
    		return;
    	}

    	$("#report-submit").addClass('disabled');

		if (!hasHwpCtrl) {
			bootbox.alert("@lang('strings.no_hwpctrl')"
   		     );
			return;
		}

  		var numAttachments = $(".uploadify-queue-item").size();
  		if (numAttachments > 0) {
  			$("#file_upload").uploadify("upload", "*");
  		} else {
  			postReport();
  		}
    });

    $("#select_template .dropdown-menu a").click(function(){
    	var type = $(this).data('value');
    	HwpCtrl.Clear(1);
    	if (type == 1)
    	{
			HwpCtrl.open("{{ url('static/misc/report1.hwp') }}");
    	}
    	else
    	{
			HwpCtrl.open("{{ url('static/misc/report2.hwp') }}");
    	}
    });
});

function postReport() {
	var params = attachments;
	var report_title = $("#report-title").val();
	var content = HwpCtrl.GetTextFile("HWP", "");
	params.push({"name": "report-title", "value": report_title});
	params.push({"name":"report-content", "value": content});
	$.ajax({
		url: "{{ action('ReportController@insertReport') }}",
		type: "post",
		data: params,
		success: function(data) {
		    window.location = "{{ action('ReportController@showList') }}";
		},
		error: function() {
			attachments = [];
			bootbox.alert("@lang('strings.failed_to_upload')"
				);
			$("#report-submit").removeClass("disabled");
		}
	});
}
</script>
@stop
