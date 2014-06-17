$.extend($.fn.dataTable.defaults, {
	language: {
		emptyTable: "자료가 없습니다",
		paginate: {
			first: "처음",
			last: "마지막",
			next: "다음",
			previous: "이전"
		},
		info: "_TOTAL_개 결과 중 _START_ ~ _END_",
		infoEmpty: "",
		infoFiltered: "전체 자료 수: _MAX_",
		thousands: ",",
		lengthMenu: "페이지당 _MENU_ 개 출력",
		loadingRecords: "자료를 불러오는 중...",
		processing: "처리 중...",
		search: "검색:",
		zeroRecords: "검색 결과가 없습니다",
		aria: {
			sortAscending: ": 오름차순 정렬을 하려면 클릭해주세요.",
			sortDescending: ": 내림차순 정렬을 하려면 클릭해주세요."
		}
	},

	pageLength: 25,

	stateSave: true
});