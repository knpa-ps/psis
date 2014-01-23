$(document).ajaxError(function(){
	noty({
		type:'error', layout:'topRight', text:"서버 오류가 발생했습니다."
	});
});
$(document).ready(function(){
	
	bootbox.backdrop(false);
	bootbox.animate(false);
	bootbox.addLocale('kr', {
		OK: "확인",
		CANCEL: "취소",
		CONFIRM: "확인"
	});
	bootbox.setLocale('kr');

	var current_theme = $.cookie('current_theme')==null ? 'classic' :$.cookie('current_theme');
	switch_theme(current_theme);
	
	$('#themes a[data-value="'+current_theme+'"]').find('i').addClass('icon-ok');
				 
	$('#themes a').click(function(e){
		e.preventDefault();
		current_theme=$(this).attr('data-value');
		$.cookie('current_theme',current_theme,{expires:365});
		switch_theme(current_theme);
		$('#themes i').removeClass('icon-ok');
		$(this).find('i').addClass('icon-ok');
	});
	
	
	function switch_theme(theme_name)
	{
		$('#bs-css').attr('href', baseUrl+'static/css/bootstrap-'+theme_name+'.css');
	}
	
	//highlight current / active link
	$('ul.main-menu li a').each(function(){
		if($($(this))[0].href==String(window.location))
			$(this).parent().addClass('active');
	});
	
	//establish history variables
	var
		History = window.History, // Note: We are using a capital H instead of a lower h
		State = History.getState(),
		$log = $('#log');

	//bind to State Change
	History.Adapter.bind(window,'statechange',function(){ // Note: We are using statechange instead of popstate
		var State = History.getState(); // Note: We are using History.getState() instead of event.state
		$.ajax({
			url:State.url,
			success:function(msg){
				$('#content').html($(msg).find('#content').html());
				$('#loading').remove();
				$('#content').fadeIn();
				var newTitle = $(msg).filter('title').text();
				$('title').text(newTitle);
				docReady();
			}
		});
	});
	
	//animating menus on hover
	$('ul.main-menu li:not(.nav-header)').hover(function(){
		$(this).animate({'margin-left':'+=5'},300);
	},
	function(){
		$(this).animate({'margin-left':'-=5'},300);
	});


	$(".select-all").click(function(){
		var b = !$(this).hasClass('active');
		var targetId = $(this).data('target');
		if (b) {
			$("#"+targetId+".multi-selectable tbody tr").addClass('row-selected');
		} else {
			$("#"+targetId+".multi-selectable tbody tr").removeClass('row-selected');
		}

	});

	//other things to do on document ready, seperated for ajax calls
	docReady();
});
function popup(url, title, w, h) {
  var left = (screen.width/2)-(w/2);
  var top = (screen.height/2)-(h/2);
  return window.open(url, title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width='+w+', height='+h+', top='+top+', left='+left);
} 
function docReady(){
	//prevent # links from moving to top
	$('a[href="#"][data-top!=true]').click(function(e){
		e.preventDefault();
	});
	$('input, textarea').placeholder();

	//datepicker
	$('.datepicker').datepicker();

	//uniform - styler for checkbox, radio and file input
	$("input:checkbox, input:radio, input:file").not('[data-no-uniform="true"],#uniform-is-ajax').uniform();

	//chosen - improves select
	$('[data-rel="chosen"],[rel="chosen"]').chosen();

	//makes elements soratble, elements that sort need to have id attribute to save the result
	$('.sortable').sortable({
		revert:true,
		cancel:'.btn,.box-content,.nav-header',
		update:function(event,ui){
			//line below gives the ids of elements, you can make ajax call here to save it to the database
			//console.log($(this).sortable('toArray'));
		}
	});

	//tooltip
	$('[rel="tooltip"],[data-rel="tooltip"]').tooltip({"placement":"bottom",delay: { show: 400, hide: 200 }});

	//popover
	$('[rel="popover"],[data-rel="popover"]').popover();

	$('.btn-close').click(function(e){
		e.preventDefault();
		$(this).parent().parent().parent().fadeOut();
	});
	$('.btn-minimize').click(function(e){
		e.preventDefault();
		var $target = $(this).parent().parent().next('.box-content');
		if($target.is(':visible')) $('i',$(this)).removeClass('icon-chevron-up').addClass('icon-chevron-down');
		else 					   $('i',$(this)).removeClass('icon-chevron-down').addClass('icon-chevron-up');
		$target.slideToggle();
	});
}

var dtOptions = {
		"bSort": false,
		"bAutoWidth":false,
		"iDisplayLength":25,
		"sDom": "<'row-fluid'<'span6'l><'span6'<'pull-right'f>>r>t<'row-fluid'<'span12'i>><'row-fluid'<'span12'<'pull-right'p>>>",
		"sPaginationType": "bootstrap",
		"oLanguage": {
			"oPaginate": {
				"sNext": "다음",
				"sPrevious": "이전",
				"sFirst": "처음",
				"sLast": "마지막"
			},	
			"sEmptyTable": "자료가 없습니다.",
			"sZeroRecords": "자료가 없습니다.",
			"sLengthMenu": "페이지당 _MENU_ 개 출력",
			"sInfoEmpty": "",
			"sInfo": "총 _TOTAL_개 중 _START_~_END_",
			"sInfoFiltered": " - 전체 _MAX_개",
			"sLoadingRecords": "자료를 가져오는 중입니다...",
			"sProcessing": "자료를 가져오는 중입니다...",
			"sSearch": "결과 내 검색:"
		}
};
