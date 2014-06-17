@extends('layouts.master')

@section('content')

<div class="row">
	<div class="col-xs-12">
		@include('widget.lastest', array('title'=> '경비예산 공지사항', 'board'=>'notice_reports', 'len'=>50))
	</div>
</div>


@stop