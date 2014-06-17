@extends('budget.mob-pay.base')

@section('tab-content')
<div class="row">
	<div class="col-xs-12">
		<div class="pull-left">
			<h4>경비동원수당 상세내역</h4>
		</div>
		<div class="btn-group pull-right">
			<a href="{{ url('/budgets/mob-pay/'.$data->id.'/edit') }}" class="btn btn-xs btn-success" id="edit_btn"><span class="glyphicon glyphicon-edit"></span> 수정</a>
			<button class="btn btn-xs btn-danger" id="delete_btn"><span class="glyphicon glyphicon-trash"></span> 삭제</button>
		</div>
		<br>
		<input type="hidden" id="master_id" value="{{ $data->id }}">
		<table class="table table-hover table-striped" id="master_table">
			<tbody>
				<tr>
					<th>집행일자</th>
					<td>{{ $data->use_date }}</td>
					<th>집행관서</th>
					<td>{{ $data->department->full_name }}</td>
					<th>자료입력자</th>
					<td>{{ $data->creator->rank->title.' '.$data->creator->user_name }}</td>
				</tr>
				<tr>
					<th>
						동원상황구분
					</th>
					<td>
						{{ $data->situation->title }}
					</td>
					<th>
						동원행사명
					</th>
					<td colspan="3">
						{{ $data->event_name }}
					</td>
				</tr>
				<tr>
					<th>총 집행액 (원)</th>
					<td colspan="5">{{ number_format($data->details()->sum('amount')) }}</td>	
				</tr>	
			</tbody>
		</table>
	</div>
</div>
<div class="row">
	<div class="col-xs-12">
		<h4>동원자 정보</h4>
		<table class="table table-hover table-striped table-bordered table-condensed" id="details_table">
			<thead>
				<tr>
					<th>관서</th>
					<th>계급</th>
					<th>이름</th>
					<th>동원기간</th>
					<th>동원수당 (원)</th>
				</tr>
			</thead>
			<tbody>
				@foreach ($data->details as $d)
					<tr>
						<td>{{ $d->department->full_name }}</td>
						<td>{{ $d->rank->title }}</td>
						<td>{{ $d->name }}</td>
						<td>{{ $d->start.' ~ '.$d->end }}</td>
						<td>{{ number_format($d->amount) }}</td>
					</tr>
				@endforeach
			</tbody>
		</table>
	</div>
</div>
@stop

@section('scripts')
<script type="text/javascript">
$(function() {
	$("#delete_btn").click(function() {
		if (!confirm('내역을 삭제하시겠습니까?')) {
			return;
		}
		var master_id = $("#master_id").val();
		if (!master_id) {
			return;
		}
		$.ajax({
			url: base_url+"/budgets/mob-pay/"+master_id,
			type: "delete",
			success: function(response) {
				alert(response.message);
				if (response.result == 0) {
					redirect(response.url);
				}
			}
		});
	});
});
</script>
@stop