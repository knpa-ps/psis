@extends('layouts.master')
<style>
a.visit:visited { color: #CECFCA; }
</style>
@section('content')

	<div class="row">
		<div class="col-xs-4">
			<img src="{{ url('/static/img/eq_main.gif') }}" alt="" class="col-xs-12" style="padding:0px; border: 1px solid transparent; box-shadow: 0 1px 1px rgba(0,0,0,0.05);" />
		</div>
		<div class="col-xs-8">
			<div class="row">
				<div class="col-xs-6">
					{{ View::make('widget.lastest', array('board'=>'notice_equip', 'title'=>'SEMS 공지사항')) }}
				</div>
				<div class="col-xs-6">
					{{ View::make('widget.lastest', array('board'=>'equip_qna', 'title'=>'SEMS 게시판')) }}
				</div>

			</div>
			{{-- 장비보급 --}}
			<div class="row">
				<div class="col-xs-12">
					<div class="panel panel-default">
						<div class="panel-heading">
							<h3 class="panel-title pull-left">
								<strong>장비보급 최근내역</strong>
							</h3>
							<a href="{{ url('equips/supplies') }}" class="label label-primary pull-right"> @lang('lastest.more') </a>
							<div class="clearfix"></div>
						</div>
						<div class="panel-body">
							{{-- <span class="label label-success">출고내역</span>
							<table class="table table-condensed table-striped table-hover">
								<thead>
									<tr>
										<th>장비명</th>
										<th>취득구분(제조사/납품일)</th>
										<th>보급일자</th>
										<th>총 보급수량</th>
									</tr>
								</thead>
								<tbody>
									@foreach ($inbounds as $i)
									<tr>
										<td>{{ $i->item->code->title }}</td>
										<td>{{ $i->fromNode->node_name }}</td>
										<td>{{ $i->converted_date }}</td>
										@if ($i->is_confirmed == 1)
										<td>
											<span class="label label-success"><span class="glyphicon glyphicon-ok"></span> {{ $i->confirmed_date }}</span>
										</td>
										@else
										<td>
											<span class="label label-danger"><span class="glyphicon glyphicon-question-sign"></span> 미확인</span>
										</td>
										@endif
									</tr>
									@endforeach
								</tbody>
							</table> --}}
							<span class="label label-warning">출고내역</span>
							<table class="table table-condensed table-striped table-hover">
								<thead>
									<th>장비명</th>
									<th>취득구분(제조사/납품일)</th>
									<th>보급일자</th>
									<th>총 보급수량</th>
								</thead>
								<tbody>
									@foreach ($supplies->reverse() as $supply)
									<tr>
										<td>
											<a href="{{ url('equips/supplies/'.$supply->id)}}" class="visit">{{ $supply->item->code->title.' / '.$supply->item->classification }}</a>
										</td>
										<td>{{ $supply->item->maker_name.' / '.$supply->item->acquired_date }}</td>
										<td>{{ $supply->supplied_date }}</td>
										<td>{{ number_format($supply->children->sum('count')) }}</td>
									</tr>
									@endforeach
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
			{{-- 관리전환 --}}
			<div class="row">
				<div class="col-xs-12">
					<div class="panel panel-default">
						<div class="panel-heading">
							<h3 class="panel-title pull-left">
								<strong>관리전환 최근내역</strong>
							</h3>
							<a href="{{ url('equips/convert') }}" class="label label-primary pull-right visit"> @lang('lastest.more') </a>
							<div class="clearfix"></div>
						</div>
						<div class="panel-body">
							<span class="label label-success">입고내역</span>
							<table class="table table-condensed table-striped table-hover">
								<thead>
									<tr>
										<th style="width: 50%">장비명</th>
										<th>출처</th>
										<th>날짜</th>
										<th style="width: 10%">확인여부</th>
									</tr>
								</thead>
								<tbody>
									@foreach ($inbounds as $i)
									<tr>
										<td style="width: 50%">{{ $i->item->code->title }}</td>
										<td style="width: 20%">{{ $i->fromNode->node_name }}</td>
										<td style="width: 20%">{{ $i->converted_date }}</td>
										@if ($i->is_confirmed == 1)
										<td style="width: 10%">
											<span class="label label-success"><span class="glyphicon glyphicon-ok"></span> {{ $i->confirmed_date }}</span>
										</td>
										@else
										<td style="width: 10%">
											<span class="label label-danger"><span class="glyphicon glyphicon-question-sign"></span> 미확인</span>
										</td>
										@endif
									</tr>
									@endforeach
								</tbody>
							</table>
							<span class="label label-warning">출고내역</span>
							<table class="table table-condensed table-striped table-hover">
								<thead>
									<tr>
										<th style="width: 50%">장비명</th>
										<th>대상</th>
										<th>날짜</th>
										<th style="width: 10%">확인여부</th>
									</tr>
								</thead>
								<tbody>
									@foreach ($outbounds as $o)
									<tr>
										<td style="width: 50%">{{ $o->item->code->title }}</td>
										<td style="width: 20%">{{ $o->targetNode->node_name }}</td>
										<td style="width: 20%">{{ $o->converted_date }}</td>
										@if ($o->is_confirmed == 1)
										<td style="width: 10%">
											<span class="label label-success"><span class="glyphicon glyphicon-ok"></span> {{ $o->confirmed_date }}</span>
										</td>
										@else
										<td style="width: 10%">
											<span class="label label-danger"><span class="glyphicon glyphicon-question-sign"></span> 미확인</span>
										</td>
										@endif
									</tr>
									@endforeach
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
			<!-- 수요조사 -->
			<div class="row">
				<div class="col-xs-12">
					<div class="panel panel-default">
						<div class="panel-heading">
							<h3 class="panel-title pull-left">
								<strong>진행중인 수요조사</strong>
							</h3>
							<a href="{{ url('equips/surveys') }}" class="label label-primary pull-right visit"> @lang('lastest.more') </a>
							<div class="clearfix"></div>
						</div>
						<div class="panel-body">
							<span class="label label-success">조사하기</span>
							<table class="table table-condensed table-striped table-hover">
								<thead>
									<tr>
										<th>장비명</th>
										<th>조사기한</th>
										<th>응답현황</th>
									</tr>
								</thead>
									@foreach ($surveys as $s)
									<tbody>
										<tr>
											<td>{{$s->item->code->title}}</td>
											<td>{{$s->started_at.'~'.$s->expired_at}}</td>
											<td>{{ number_format($s->responses->sum('count')).' / '.number_format($s->datas->sum('count')).' ('.round($s->responses->sum('count')/$s->datas->sum('count')*100).'%)' }}</td>
										</tr>
									</tbody>
									@endforeach
							</table>
							<span class="label label-warning">조사응답</span>
							<table class="table table-condensed table-striped table-hover">
								<thead>
									<tr>
										<th>장비명</th>
										<th>조사기한</th>
										<th>응답여부</th>
									</tr>
								</thead>
								<tbody>
								@foreach ($toResponses as $r)
									<tr>
										<td>{{$r->item->code->title}}</td>
										<td>{{$r->started_at.'~'.$r->expired_at}}</td>
										<td>
										@if ($r->isResponsed($user->supplyNode->id))
											<span class="label label-success">설문응답완료</span>
										@else
											<span class="label label-danger">미응답</span>
										@endif
										</td>
									</tr>
								@endforeach
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

@stop
