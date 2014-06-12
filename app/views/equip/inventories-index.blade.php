@extends('layouts.master')

@section('content')
<div class="row">
	<div class="col-xs-12">
		<div class="panel panel-default">
			<div class="panel-body">
				<table class="table table-condensed table-bordered table-striped table-hover" id="data_table">
					<thead>
						<tr>
							<th>
								분류
							</th>
							<th>
								장비명
							</th>
							<th>
								모델명
							</th>
							<th>
								취득시기
							</th>
							<th>
								취득경로
							</th>
							<th>
								보유량 (개)
							</th>
						</tr>
					</thead>
					<tbody>
						@foreach ($inventories as $i)
							<tr>
								<td>
									{{ $i->item->category->name or '' }}
								</td>
								<td>
									{{ $i->item->name or '' }}
								</td>
								<td>
									{{ $i->model_name }}
								</td>
								<td>
									{{ $i->acq_date }}
								</td>
								<td>
									{{ $i->acq_route }}
								</td>
								<td>
									{{ number_format($i->count) }}
								</td>
							</tr>
						@endforeach
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
@stop

@section('scripts')
{{ HTML::dataTables(); }}
<script type="text/javascript">
$(function() {
	$("#data_table").DataTable({

	});
});
</script>
@stop