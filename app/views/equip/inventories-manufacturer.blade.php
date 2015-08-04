@extends('layouts.master')

@section('content')
<div class="row">
	<div class="col-xs-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">
					<b>제조업체 정보</b>
				</h3>
			</div>
			<div class="panel-body">
				<table class="table table-condensed table-bordered table-striped">
					<colgroup>
						<col class="col-xs-2">
						<col class="col-xs-10">
					</colgroup>
					<tr>
						<th>업체명</th>
						<td>{{ $i->manufacturer}}</td>
					</tr>
					<tr>
						<th>전화번호</th>
						<td>{{ $i->manufacturer_phone }}</td>
					</tr>
					<tr>
						<th>주소</th>
						<td>{{ $i->manufacturer_address }}</td>
					</tr>
				</table>
			</div>
		</div>
	</div>
</div>
@stop