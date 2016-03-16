@extends('layouts.master')

@section('content')

<div class="row">
	<div class="col-xs-12">
		{{ View::make('widget.lastest', array('board'=>'notice_eventreports', 'title'=>'선거 및 행사속보 공지사항')) }}
	</div>
</div>

@stop
