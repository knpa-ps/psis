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
								번호
							</th>
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
								총 취득수량 (개)
							</th>
							<th>
								총 보급수량 (개)
							</th>
							<th>
								잔여수량 (개)
							</th>
						</tr>
					</thead>
					<tbody>
						@foreach ($items as $i)
							<tr>
								<td>
									{{ $i->id or '' }}
								</td>
								<td>
									{{ $i->category->name or '' }}
								</td>
								<td>
									<a href="{{ url('equips/items/'.$i->id) }}">{{ $i->name or '' }}</a>
								</td>
								<td>
									{{ $i->maker_name or '' }}
								</td>
								<td>
									{{ $having_count[$i->id] or '' }}
								</td>
								<td>
									{{ $supplied_count[$i->id] or '' }}
								</td>
								<td>
									{{ $remaining_count[$i->id] or '' }}
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