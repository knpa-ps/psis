<?php 
$codes = Code::where('category_code','=',$category)->visible()->orderBy('sort_order', 'asc')->get();
$blank = isset($blank) ? $blank : true;
$default = isset($default) ? $default : "";
if ($blank) {
	$selects = array(""=>"전체");
} else {
	$selects = array();
}

foreach ($codes as $code) {
	$selects[$code->code] = $code->title;
}

$opts = array_merge(array(
	'class'=>'form-control input-sm',
	'id'=>$id
	), isset($options) ? $options : array());
?>

{{ Form::select($id, $selects, $default, $opts) }}