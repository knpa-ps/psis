$.extend($.fn.datepicker.defaults, {
	format: "yyyy-mm-dd",
	todayBtn: "linked",
	language: "kr",
	autoclose: true,
    multidate: false,
	todayHightlight: true
});

$(function() {
	$(".input-daterange, .input-datepicker").datepicker();

});