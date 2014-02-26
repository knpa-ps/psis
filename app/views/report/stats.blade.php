@extends('layouts.master')

@section('content')
<div class="row-fluid">
	<div class="span12 well well-small">
		<form class="form form-horizontal form-query" id="q_form">
			<div class="header">
				<h4>조회조건</h4>
			</div>
			<div class="row-fluid">
				<div class="span6">
					
					<div class="control-group">
						<label for="q_date" class="control-label">
							조회기간 
						</label>
						<div class="controls">
							<input type="text" class="input-small datepicker start" name="q_date_start" id="q_date_start"> ~ 
							<input type="text" class="input-small datepicker end" id="q_date_end" name="q_date_end">
						</div>
					</div>
				</div>
				<div class="span6">
					
					
					<div class="control-group">
						<label for="q_department" class="control-label">
							관서명 
						</label>
						<div class="controls">
							<div class="input-append">
						        <input type="text" id="q_department" name="q_department" class="input-large"><button class="btn" type="button" id="dept-search"
						            onclick="popup('{{action('DepartmentController@showDeptTree')}}', '', 500, 800)">
						            @lang('strings.select')
						        </button>
					        </div>
						</div>
					</div>
					
				</div>
			</div>
			<div class="row-fluid">
				<div class="span12">
					<div class="control-group pull-right">
						<div class="controls">
							<button type="button" class="btn btn-primary" id="q_form_submit">
								@lang('strings.view')
							</button>
						</div>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>

<div class="row-fluid">		
	<div class="box span12">
		<div class="box-header well" data-original-title>
			<h2><i class="icon-th-list"></i> @lang('strings.report_list')</h2>
			<div class="box-icon">
				
			</div>
		</div>
		<div class="box-content">
			<ul class="nav nav-tabs" id="table_tabs">
			  <li class="active"><a href="#indiv">개인별 사용통계</a></li>
			  <li><a href="#dept">부서별 사용통계</a></li>
			</ul>
			 
			<div class="tab-content">
				<div class="tab-pane active" id="indiv">
			  		<table class="table table-condensed table-bordered table-striped table-hover">
			  			<thead>
			  				<tr>
			  					<th>소속</th>
			  					<th>계급</th>
			  					<th>이름</th>	
			  					<th>작성 속보 수</th>
			  				</tr>
			  			</thead>
			  			<tbody>
			  				<tr>
			  					<td>서울청 종로경찰서 경비계</td>
			  					<td>순경</td>
			  					<td>노민종</td>
			  					<td>43</td>
			  				</tr>
			  				<tr>
			  					<td>서울청 종로경찰서 경비계</td>
			  					<td>순경</td>
			  					<td>노민종</td>
			  					<td>43</td>
			  				</tr>
			  				<tr>
			  					<td>서울청 종로경찰서 경비계</td>
			  					<td>순경</td>
			  					<td>노민종</td>
			  					<td>43</td>
			  				</tr>
			  				<tr>
			  					<td>서울청 종로경찰서 경비계</td>
			  					<td>순경</td>
			  					<td>노민종</td>
			  					<td>43</td>
			  				</tr>
			  				<tr>
			  					<td>서울청 종로경찰서 경비계</td>
			  					<td>순경</td>
			  					<td>노민종</td>
			  					<td>43</td>
			  				</tr>
			  				<tr>
			  					<td>서울청 종로경찰서 경비계</td>
			  					<td>순경</td>
			  					<td>노민종</td>
			  					<td>43</td>
			  				</tr>
			  				<tr>
			  					<td>서울청 종로경찰서 경비계</td>
			  					<td>순경</td>
			  					<td>노민종</td>
			  					<td>43</td>
			  				</tr>
			  				<tr>
			  					<td>서울청 종로경찰서 경비계</td>
			  					<td>순경</td>
			  					<td>노민종</td>
			  					<td>43</td>
			  				</tr>
			  				<tr>
			  					<td>서울청 종로경찰서 경비계</td>
			  					<td>순경</td>
			  					<td>노민종</td>
			  					<td>43</td>
			  				</tr>
			  				<tr>
			  					<td>서울청 종로경찰서 경비계</td>
			  					<td>순경</td>
			  					<td>노민종</td>
			  					<td>43</td>
			  				</tr>
			  			</tbody>
			  		</table>
				</div>
				<div class="tab-pane" id="dept">
			  		<table class="table table-condensed table-bordered table-striped table-hover">
			  			<thead>
			  				<tr>
			  					<th>관서</th>
			  					<th>작성 속보 수</th>
			  				</tr>
			  			</thead>
			  			<tbody>
			  				<tr>
			  					<td>서울청 종로경찰서 경비계</td>
			  					<td>43</td>
			  				</tr>
			  				<tr>
			  					<td>서울청 종로경찰서 경비계</td>
			  					<td>43</td>
			  				</tr>
			  				<tr>
			  					<td>서울청 종로경찰서 경비계</td>
			  					<td>43</td>
			  				</tr>
			  				<tr>
			  					<td>서울청 종로경찰서 경비계</td>
			  					<td>43</td>
			  				</tr>
			  				<tr>
			  					<td>서울청 종로경찰서 경비계</td>
			  					<td>43</td>
			  				</tr>
			  				<tr>
			  					<td>서울청 종로경찰서 경비계</td>
			  					<td>43</td>
			  				</tr>
			  				<tr>
			  					<td>서울청 종로경찰서 경비계</td>
			  					<td>43</td>
			  				</tr>
			  			</tbody>
			  		</table>
				</div>
			</div>
		</div>
	</div>
</div>
@stop

@section('styles')
@stop

@section('scripts')
<script type="text/javascript">
$(document).ready(function(){
    $('#table_tabs a').click(function(e){
    	e.preventDefault();
    	$(this).tab('show');
    });
});
</script>
@stop
