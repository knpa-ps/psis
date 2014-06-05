@extends('layouts.master')


@section('content')

<div class="row">
	<div class="col-xs-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title"><strong>장비 목록</strong></h3>
			</div>
			<div class="panel-body">
				<div class="toolbar-table">
					<a href="{{ url('equips/items/create') }}" class="btn btn-info btn-xs pull-right">
						<span class="glyphicon glyphicon-plus"></span> 장비추가
					</a>
					<div class="clearfix"></div>
				</div>
				<table class="table table-condensed table-bordered table-striped table-hover" id="items_table">
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
								제원
							</th>
							<th>
								단위
							</th>
							<th>
								내구연한
							</th>
						</tr>
					</thead>
					<tbody>
						@foreach ($items as $i)
						<tr data-id="{{ $i->id }}">
							<td> {{ $i->id }} </td>
							<td> [{{ $i->category->domain->name }}] {{ $i->category->name }} </td>
							<td> <a href="{{ url('equips/items/'.$i->id) }}">{{ $i->name }}</a> </td>
							<td> {{ $i->standard }} </td>
							<td> {{ $i->unit }} </td>
							<td> {{ $i->persist_years }} </td>
						</tr>
						@endforeach
					</tbody>
				</table>

				{{ $items->links() }}
			</div>
		</div>
	</div>
</div>

@stop
@section('styles')
@stop
@section('scripts')
<script type="text/javascript">
$(function() {

});
</script>
@stop

