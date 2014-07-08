@extends('layouts.master')

@section('content')
<div class="row">
	<div class="col-xs-12">
		<div class="panel panel-default">
			<div class="panel-body">
				<div class="toolbar-table">
					<a href="{{url('equips/inventories/create')}}" class="btn btn-info btn-xs pull-right">
						<span class="glyphicon glyphicon-plus"></span> 취득장비추가
					</a>	
					<div class="clearfix"></div>
				</div>
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
								제조업체명
							</th>
							<th>
								취득시기
							</th>
							<th>
								취득경로
							</th>
							<th>
								취득수량 (개)
							</th>
							<th>
								보급수량 (개)
							</th>
							<th>
								보유수량 (개)
							</th>
							<th>
								작업
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
									<a href="{{ url('equips/items/'.$i->item->id) }}">{{ $i->item->name or '' }}</a>
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
								<td>
									{{ number_format($sum[$i->id]) }}
								</td>
								<td>
									{{ number_format($i->count-$sum[$i->id]) }}
								</td>
								<td>
									<a id="{{ $i->id }}" href="#" class="delete label label-danger pull-right">
										<span class="glyphicon glyphicon-trash"></span> 삭제
									</a>
									<a id="{{ $i->id }}" href="{{url('equips/inventories/'.$i->id.'/edit')}}" class="update label label-success pull-right">
										<span class="glyphicon glyphicon-pencil"></span> 수정
									</a>
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

	$(".delete").on('click', function(){
		if(!confirm('정말 삭제하시겠습니까?')){
			return;
		}
		$.ajax({
			url: base_url+"/equips/inventories/"+this.id,
			method : 'delete',
			success : function(res) {
				alert(res);
				window.location.reload();
			}
		});
	});
});
</script>
@stop