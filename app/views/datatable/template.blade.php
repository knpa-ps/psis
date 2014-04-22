<table class="table table-condensed table-hover table-striped table-bordered {{ $class or '' }}" id="{{ $id }}">
    <colgroup>
        @for ($i = 0; $i < count($columns); $i++)
        <col class="col-{{ $i }}" />
        @endfor
    </colgroup>
    <thead>
    <tr>
        @foreach($columns as $i => $c)
        	<th align="center" valign="middle" class="head-{{ $i }}">{{ $c }}</th>
        @endforeach
    </tr>
    </thead>
    <tbody>
    
    </tbody>
</table>
