<?php

$id = isset($id) ? $id : 'dept_id';
$class = isset($class) ? $class : '';
$defaultDeptName = isset($default)? $default->full_name : '';
$defaultDeptId = isset($default) ? $default->id : '';

?>

<div class="has-feedback dept-selector {{ $class }}" id="{{ $id }}_container">

    <input type="text" readonly="readonly" class="form-control dept-name" name="{{ $id }}_display" id="{{ $id }}_display" value="{{ $defaultDeptName }}">
    
    <input type="hidden" name="{{ $id }}" id="{{ $id }}" class="dept-id" value="{{$defaultDeptId}}">
    
    <a href="#" class="dept-selector-clear"><span class="glyphicon glyphicon-remove form-control-feedback"></span></a>

</div>

