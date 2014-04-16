@extends('layouts.master')


@section('content')

<div class="row-fluid">		
	<div class="box span12">
		<div class="box-header well" data-original-title>
			<h2><i class="icon-user"></i> @lang('strings.user_detail')</h2>
			<div class="box-icon">

			</div>
		</div>
		<div class="box-content">
			<form class="form" novalidate>
			<table class="table table-hover table-striped table-form">
				
				<tbody>
					<tr>
						<th>
							@lang('strings.account_name')
						</th>
						<td>
							<span class="uneditable-input input-large">
							</span>
						</td>
					</tr>

					<tr>
						<th>
							@lang('strings.user_name')
						</th>
						<td>
							<input type="text" name="user_name" required maxlength="10">
						</td>
					</tr>
					
				</tbody>
			</table>
			</form>
		</div>

	</div>
</div>
@stop

@section('script')

@stop