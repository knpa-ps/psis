@extends('layouts.master')

@section('navbar')
	@include('parts.navbar')
@stop

@section('sidebar')
	@include('parts.sidebar')
@stop

@section('content-header')
    <div class="row" id="content-wrapper">
	    <div class="col-xs-12">
    	@if ($breadcrumbs)
	        <div class="row" id="breadcrumb-wrapper">
	            <ol class="breadcrumb" id="breadcrumb">
            		@foreach ($breadcrumbs as $idx=>$menu)
            			@if ($idx == count($breadcrumbs)-1)
            				<li class="active">{{ $menu->name }}</li>	
            			@else
							<li><a href="{{ action($menu->action->action) }}">{{ $menu->name }}</a></li>	
            			@endif
            			
            		@endforeach
	            </ol>
	        </div>
		@endif
	        <div class="row">
	            <div class="col-xs-12" id="content">
@stop

@section('content-footer')
                </div>
            </div>
        </div>
    </div>
@stop

