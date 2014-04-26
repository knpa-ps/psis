@extends('layouts.base')

@section('body')

{{-- bootstrap-modal 플러그인을 위한 wrapper --}}
<div class="page-container">

    @include('parts.header')

    <div class="container" id="container">
        <div class="row">

            <div class="col-xs-2">
                @include('parts.sidebar')
                @section('sidebar')
                @show
            </div>

            <div class="col-xs-10" id="content">
                @yield('content')
            </div>
            
        </div>
    </div>

    @include('parts.footer')

    <div id="ajax_modal" class="modal fade" tabindex="-1" style="display: none;"></div>
</div>

@stop
