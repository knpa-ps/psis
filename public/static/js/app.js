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

function popup(url, w, h) {
  var left = (screen.width/2)-(w/2);
  var top = (screen.height/2)-(h/2);
  return window.open(url, '','toolbar=no, location=no, directories=no, status=no, menubar=no, copyhistory=no, width='+w+', height='+h+', top='+top+', left='+left);
}

function redirect(url) {
	window.location.href = url;
}