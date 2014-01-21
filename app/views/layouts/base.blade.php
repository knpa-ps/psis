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
    	
	        <div class="row">
	            <div class="col-xs-12" id="content">
@stop

@section('content-footer')
                </div>
            </div>
        </div>
    </div>
@stop

