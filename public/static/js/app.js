$(function() {
	//prevent # links from moving to top
	$('a[href="#"][data-top!=true]').click(function(e){
		e.preventDefault();
	});
	
	// $('input, textarea').placeholder();

	// //datepicker
	// $(".datepicker").datepicker({
	// 	changeYear:true,
	// 	changeMonth:true,
	// 	dateFormat:"yy-mm-dd",
	// 	dayNamesMin: [ "월", "화", "수", "목", "금", "토", "일" ],
	// 	monthNamesShort: [ "1", "2", "3", "4", "5", "6", "7", "8", "9", "10", "11", "12" ],
	// 	showMonthAfterYear:true
	// });

	// var today = new Date();
	// var fd = new Date(today.getFullYear(), today.getMonth(), 1);
	// var ld = new Date(today.getFullYear(), today.getMonth()+1, 0);
	// $(".datepicker.start").datepicker("setDate", fd);
	// $(".datepicker.end").datepicker("setDate", ld);
	$(".board-link").click(function(e) {
		e.preventDefault();
		var url = $(this).prop('href');
		if (!url) {
			return;
		}
		popup(url, 900, 800);
	});
});


// dept-selector
$(function() {
	var $modal = $("#ajax_modal");

	$(".dept-selector-clear").on('click', function() {
		$(this).siblings(".dept-name").val("");
		$(this).siblings(".dept-id").val("");
		$(this).parent().trigger('clear.dept-selector');
	});

	// dept-selector modal 띄우기
	$(".dept-name").on('click', function() {

		$("body").modalmanager('loading');

		var container_id = $(this).parent().prop('id');

		var data = {
			container_id: container_id
		};

		$modal.load(base_url+"/ajax/dept_select_tree", data, function() {
			$modal.modal({
				modalOverflow: true
			});
		});
	});
	
	$(".dept-selector").on('select.dept-selector', function(e, data) {
		$(this).find(".dept-name").val(data.full_name);
		$(this).find(".dept-id").val(data.dept_id);
	});
});

// bootstrap-modal
$.fn.modal.defaults.maxHeight = function(){
    // subtract the height of the modal header and footer
    return $(window).height() - 165; 
}
$.fn.modal.defaults.spinner = $.fn.modalmanager.defaults.spinner = 
    '<div class="loading-spinner" style="width: 200px; margin-left: -100px;">' +
        '<div class="progress progress-striped active">' +
            '<div class="progress-bar" style="width: 100%;"></div>' +
        '</div>' +
    '</div>';


function popup(url, w, h) {
  var left = (screen.width/2)-(w/2);
  var top = (screen.height/2)-(h/2);
  return window.open(url, '','toolbar=no, location=no, directories=no, status=no, menubar=no, copyhistory=no, width='+w+', height='+h+', top='+top+', left='+left);
}

function redirect(url) {
	window.location.href = url;
}