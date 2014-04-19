@extends('layouts.master')

@section('content')

<div class="row-fluid">
	<div class="span12">
		@foreach ($groups as $group) 
		<p>{{ $group->name }}</p>
		@endforeach
	</div>
</div>

<div class="row-fluid">
	<div class="span12">
		{{ $groups->links() }}
	</div>
</div>

@stop

@section('scripts')
<script type="text/javascript">
$(function () { 

});
</script>
@stop