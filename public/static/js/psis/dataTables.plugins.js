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

$(document).on('click', '.datatable.multi-selectable tbody tr', function(){
    $(this).toggleClass('row-selected');
});
$(document).on('click', '.datatable.single-selectable tbody tr', function(){
    if ( $(this).hasClass('row-selected') ) {
        $(this).removeClass('row-selected');
    }
    else {
        $(this).closest('.datatable.single-selectable').find('tr.row-selected').removeClass('row-selected');
        $(this).addClass('row-selected');
    }
});

function fnGetSelected( oTableLocal )
{
    return oTableLocal.$('tr.row-selected');
}
//additional functions for data table
$.fn.dataTableExt.oApi.fnPagingInfo = function ( oSettings )
{
	return {
		"iStart":         oSettings._iDisplayStart,
		"iEnd":           oSettings.fnDisplayEnd(),
		"iLength":        oSettings._iDisplayLength,
		"iTotal":         oSettings.fnRecordsTotal(),
		"iFilteredTotal": oSettings.fnRecordsDisplay(),
		"iPage":          Math.ceil( oSettings._iDisplayStart / oSettings._iDisplayLength ),
		"iTotalPages":    Math.ceil( oSettings.fnRecordsDisplay() / oSettings._iDisplayLength )
	};
}
$.extend( $.fn.dataTableExt.oPagination, {
	"bootstrap": {
		"fnInit": function( oSettings, nPaging, fnDraw ) {
			var oLang = oSettings.oLanguage.oPaginate;
			var fnClickHandler = function ( e ) {
				e.preventDefault();
				if ( oSettings.oApi._fnPageChange(oSettings, e.data.action) ) {
					fnDraw( oSettings );
				}
			};

			$(nPaging).addClass('pagination').append(
				'<ul>'+
					'<li class="prev disabled"><a href="#">&larr; '+oLang.sPrevious+'</a></li>'+
					'<li class="next disabled"><a href="#">'+oLang.sNext+' &rarr; </a></li>'+
				'</ul>'
			);
			var els = $('a', nPaging);
			$(els[0]).bind( 'click.DT', { action: "previous" }, fnClickHandler );
			$(els[1]).bind( 'click.DT', { action: "next" }, fnClickHandler );
		},

		"fnUpdate": function ( oSettings, fnDraw ) {
			var iListLength = 5;
			var oPaging = oSettings.oInstance.fnPagingInfo();
			var an = oSettings.aanFeatures.p;
			var i, j, sClass, iStart, iEnd, iHalf=Math.floor(iListLength/2);

			if ( oPaging.iTotalPages < iListLength) {
				iStart = 1;
				iEnd = oPaging.iTotalPages;
			}
			else if ( oPaging.iPage <= iHalf ) {
				iStart = 1;
				iEnd = iListLength;
			} else if ( oPaging.iPage >= (oPaging.iTotalPages-iHalf) ) {
				iStart = oPaging.iTotalPages - iListLength + 1;
				iEnd = oPaging.iTotalPages;
			} else {
				iStart = oPaging.iPage - iHalf + 1;
				iEnd = iStart + iListLength - 1;
			}

			for ( i=0, iLen=an.length ; i<iLen ; i++ ) {
				// remove the middle elements
				$('li:gt(0)', an[i]).filter(':not(:last)').remove();

				// add the new list items and their event handlers
				for ( j=iStart ; j<=iEnd ; j++ ) {
					sClass = (j==oPaging.iPage+1) ? 'class="active"' : '';
					$('<li '+sClass+'><a href="#">'+j+'</a></li>')
						.insertBefore( $('li:last', an[i])[0] )
						.bind('click', function (e) {
							e.preventDefault();
							oSettings._iDisplayStart = (parseInt($('a', this).text(),10)-1) * oPaging.iLength;
							fnDraw( oSettings );
						} );
				}

				// add / remove disabled classes from the static elements
				if ( oPaging.iPage === 0 ) {
					$('li:first', an[i]).addClass('disabled');
				} else {
					$('li:first', an[i]).removeClass('disabled');
				}

				if ( oPaging.iPage === oPaging.iTotalPages-1 || oPaging.iTotalPages === 0 ) {
					$('li:last', an[i]).addClass('disabled');
				} else {
					$('li:last', an[i]).removeClass('disabled');
				}
			}
		}
	}
});
