<?php

$id = isset($id) ? $id : 'dept_id';//이부분
$class = isset($class) ? $class : '';//이부분
$defaultDeptName = isset($default['full_name']) ? $default['full_name'] : '관서를 선택해주세';
$defaultDeptId = isset($default['id']) ? $default['id'] : '';
$inputClass = isset($inputClass) ? $inputClass : '';//이부분
$initNodeId = isset($initNodeId) ? $initNodeId : 1;//이부분 들은 값도 안들어오는데 어떤 역할을 하는지 잘 모르겠음
?>

<div class="has-feedback dept-selector {{ $class }}" id="{{ $id }}_container" <?php echo (isset($mngDeptId))? 'mngdeptid='.$mngDeptId : ''; ?> <?php echo (isset($initNodeId))? 'initnodeid='.$initNodeId : ''; ?> >
    <input type="text" readonly="readonly" class="form-control dept-name {{ $inputClass }}"
    name="{{ $id }}_display" id="{{ $id }}_display" value="{{ $defaultDeptName }}">
    <input type="hidden" name="{{ $id }}" id="{{ $id }}" class="dept-id" value="{{$defaultDeptId}}">

    <a href="#" class="dept-selector-clear"><span class="glyphicon glyphicon-remove form-control-feedback"></span></a>

</div>
