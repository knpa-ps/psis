@extends('layouts.master')

@section('content')
<div class="row">
	<div class="col-xs-6">
		@include('widget.lastest', array('title'=> Lang::get('global.t_notice'), 'board'=>'notice'))
	</div>
	<div class="col-xs-6">
		@include('widget.lastest', array('title'=> Lang::get('global.t_qna'), 'board'=>'qna'))
	</div>
</div>


@stop