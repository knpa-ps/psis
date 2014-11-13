@extends('layouts.master')

@section('content')

<div class="row">
	<div class="col-xs-12">
		<div class="panel-default panel">
			<div class="panel-heading">
				<h3 class="panel-title">
					<strong>입출고 이력</strong>
				</h3>
			</div>
			<div class="panel-body">
				<div class="row toolbar-table">
					<div class="col-xs-12">
						<div class="pull-right">
							<a href="{{ url('equips/items/'.$itemId.'/discard') }}" class="btn btn-warning btn-xs">
								<span class="glyphicon glyphicon-trash"></span> 분실/폐기 등록
							</a>
						</div>
						<div class="clearfix"></div>
					</div>
				</div>
				<table class="table table-condensed table-bordered table-hover table-striped display" id="data_table">
					<thead>
						<tr>
							<th>
								날짜
							</th>
							<th>
								입고수량
							</th>
							<th>
								출고수량
							</th>
							<th>
								비고
							</th>
						</tr>
					</thead>
					<tfoot>
						<th>
							현재 보유수량
						</th>
						<th>
							{{ $remaining }}
						</th>
						<th>
							
						</th>
						<th>
							
						</th>
					</tfoot>
					<tbody>
					@if (count($elements) > 0)
						@foreach ($elements as $e)
							<tr>
								<td>
									{{ $e->date }}
								</td>
								<td>
									{{ $e->income==0 ? '' : $e->income }}
								</td>
								<td>
									{{ $e->outgoings == 0 ? '' : $e->outgoings }}
								</td>
								<td>
									{{ $e->classification }}
								</td>
							</tr>
						@endforeach
					@else
						<tr>
							<td colspan="4">
								내역이 없습니다.
							</td>
						</tr>
					@endif
					</tbody>
				</table>
				{{ $elements->links() }}
			</div>
		</div>
	</div>
</div>

@stop

@section('styles')
<style type="text/css" media="screen">
	.form-delete {
		display:inline-block;
	}
</style>
@stop

@section('scripts')
{{ HTML::dataTables() }}
<script type="text/javascript">
$(function() {
	$("#data_table").DataTable();
});
</script>
@stop
