<?php

$id = isset($id) ? $id : 'dept_id';
$class = isset($class) ? $class : '';
$defaultDeptName = isset($full_name) ? $full_name : '';
$defaultDeptId = isset($default['id']) ? $default['id'] : '';
$inputClass = isset($inputClass) ? $inputClass : '';
$initNodeId = isset($initNodeId) ? $initNodeId : 1;
?>

<div class="has-feedback dept-selector {{ $class }}" id="{{ $id }}_container" <?php echo (isset($mngDeptId))? 'mngdeptid='.$mngDeptId : ''; ?> <?php echo (isset($initNodeId))? 'initnodeid='.$initNodeId : ''; ?> >
    <input type="text" readonly="readonly" class="form-control dept-name {{ $inputClass }}"
    name="{{ $id }}_display" id="{{ $id }}_display" value="{{ $defaultDeptName }}">
    <input type="hidden" name="{{ $id }}" id="{{ $id }}" class="dept-id" value="{{$defaultDeptId}}">

    <a href="#" class="dept-selector-clear"><span class="glyphicon glyphicon-remove form-control-feedback"></span></a>

</div>
