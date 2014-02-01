@extends('layouts.master')

@section('content')
            
<div class="row-fluid">
    <div class="box span12">
        <div class="box-header well">
            <h2><i class="icon-info-sign"></i> @lang('strings.site_title')</h2>
            <div class="box-icon">
                <a href="#" class="btn btn-minimize btn-round"><i class="icon-chevron-up"></i></a>
                <a href="#" class="btn btn-close btn-round"><i class="icon-remove"></i></a>
            </div>
        </div>
        <div class="box-content">
            <p>본 시스템은 IE8 이상에서 정상적으로 작동합니다.</p>
            <div class="clearfix"></div>
        </div>
    </div>
</div>
 
@stop