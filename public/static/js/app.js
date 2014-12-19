

$(document).on('click', 'table.multi-selectable tbody tr', function(){
    $(this).toggleClass('row-selected info');
});

$(document).on('click', 'table.single-selectable tbody tr', function(){
    if ( $(this).hasClass('row-selected') ) {
        $(this).removeClass('row-selected info');
    } else {
        $(this).closest('table.single-selectable').find('tr.row-selected').removeClass('row-selected info');
        $(this).addClass('row-selected info');
    }
});

$(document).on('click', '.select-all', function() {
	var checked = $(this).hasClass('active');
	var target_table_id = $(this).data('target');
	var target_rows = $("#"+target_table_id+" tbody tr");
	var icon = $(this).find('span.glyphicon');
	if (checked) {
		icon.addClass('glyphicon-check').removeClass('glyphicon-unchecked');
		target_rows.addClass('row-selected info');
	} else {
		icon.addClass('glyphicon-unchecked').removeClass('glyphicon-check');
		target_rows.removeClass('row-selected info');
	}
});

$(function() {
	$('a[href="#"][data-top!=true]').click(function(e){
		e.preventDefault();
	});
	
	$(".board-link").click(function(e) {
		e.preventDefault();
		var url = $(this).prop('href');
		if (!url) {
			return;
		}
		popup(url, 900, 800);
	});

	$("#logout").on('click', function() {
		if (!confirm('로그아웃 하시겠습니까?')) {
			return;
		}

		redirect(base_url+"/logout");
	});
});

$( document ).ajaxError(function( event, jqxhr, settings, exception ) {
	if (jqxhr.getAllResponseHeaders()) 
		alert('서버에서 오류가 발생했습니다.\n잠시 후 다시 시도해주세요.');
});

var $modal;
// dept-selector
$(function() {
	$modal = $("#ajax_modal");

	$(".dept-selector-clear").on('click', function() {
		$(this).siblings(".dept-name").val("");
		$(this).siblings(".dept-id").val("");
		$(this).parent().trigger('clear.dept-selector');
	});

	// dept-selector modal 띄우기
	$(".dept-name").on('click', function() {
		$("body").modalmanager('loading');

		var container_id = $(this).parent().prop('id');
		var mng_dept_id = $(this).parent().attr('mngdeptid');
		var init_node_id = $(this).parent().attr('initnodeid');
		var data = {
			container_id: container_id,
			id : init_node_id
		};
		if (typeof mng_dept_id != 'undefined') {
			data.mngDeptId = mng_dept_id;
		};

		if (!$(".dept-name").hasClass("select-node")) {
			$modal.load(base_url+"/ajax/dept_select_tree", data, function() {
				$modal.modal({
					modalOverflow: true
				});
			});
		} else {
			$modal.load(base_url+"/ajax/node_select_tree", data, function() {
				$modal.modal({
					modalOverflow: true
				});
			});
		}
		
	});
	
	$(".dept-selector").on('select.dept-selector', function(e, data) {
		$(this).find(".dept-name").val(data.full_name);
		$(this).find(".dept-id").val(data.dept_id);
	});

	$(".update_personnel").on('click', function(){
		popup(base_url+'/equips/update_personnel/show', 400, 250);
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
  return window.open(url, '','toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, copyhistory=no, width='+w+', height='+h+', top='+top+', left='+left);
}

function redirect(path) {
	if (!path) {
		return;
	}

	if (path.match(/^http\:\/\//g) == null) {
		path = url(path);
	}

	window.location.href = path;
}

function url(path) {
	return base_url + "/" + path.replace(/^\//, '');
}

