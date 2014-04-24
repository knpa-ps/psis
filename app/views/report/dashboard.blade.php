@extends('layouts.master')

@section('content')

<div class="row">
	<div class="col-xs-6">
		{{ View::make('widget.lastest', array('board'=>'notice_reports', 'title'=>'공지사항-경비속보')) }}
	</div>
</div>

@stop