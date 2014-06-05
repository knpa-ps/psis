@extends('layouts.master')

@section('content')

<div class="row">
	<div class="col-xs-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title"><strong>장비분류관리</strong></h3>
			</div>
			<div class="panel-body">
				<div class="row toolbar-table">
					<div class="col-xs-12">
						<div class="pull-right">
							<a href="{{ url('equips/categories/create') }}" class="btn btn-info btn-xs">
								<span class="glyphicon glyphicon-plus"></span> 분류추가
							</a>
						</div>
						<div class="clearfix"></div>
					</div>
				</div>

				<div class="row">
					<div class="col-xs-12">
						<table class="table table-condensed table-hover table-striped table-bordered single-selectable" id="categories_table">
							<thead>
								<tr>
									<th>번호</th>
									<th>기능</th>
									<th>이름</th>
									<th>생성일</th>
									<th>작업</th>	
								</tr>
							</thead>
							<tbody>
								@foreach ($categories as $c)
								<tr data-id="{{ $c->id }}">
									<td>
										{{ $c->id }}
									</td>
									<td>
										{{ $c->domain->name }}
									</td>
									<td>
										{{ $c->name }}
									</td>
									<td>
										{{ $c->created_at }}
									</td>	
									<td>
										<a href="{{ url('equips/categories/'.$c->id) }}" class="label label-success btn-edit">
											<span class="glyphicon glyphicon-edit"></span> 수정
										</a>
										<a href="#" class="label label-danger btn-delete">
											<span class="glyphicon glyphicon-remove"></span> 삭제
										</a>
									</td>
								</tr>
								@endforeach
							</tbody>
						</table>

						{{ $categories->links() }}
					</div>
				</div>

			</div>
		</div>
	</div>
</div>

@stop

@section('scripts')
<script type="text/javascript">
$(function() {
	$(".btn-edit").on('click', function() {

	});

	$(".btn-delete").on('click', function() {
		if (!confirm('정말 삭제하시겠습니까?')) {
			return;
		}

		var id = $(this).parent().parent().data('id');

		$.ajax({
			url: url("equips/categories/"+id),
			type: "delete",
			success: function(res) {
				alert(res.message);

				if (res.result == 0) {
					window.location.reload();
				}
			}
		});
	});
});
</script>
@stop