<?php
	/**
	 * 최근 글 위젯
	 * $board : 그누보드 게시판 아이디
	 * $count : 출력 카운트 (기본 5)
	 * $len : 제목 길이 (기본 20)
     */
	$service = new BoardService;

	$writes = $service->getLastest($board, isset($count) ? $count : 5, isset($len) ? $len : 20);

?>
<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title pull-left"><strong>{{ $title }}</strong></h3>
		<a href="{{ board_url($board) }}" class="label label-primary pull-right board-link"> @lang('lastest.more') </a>
		<div class="clearfix"></div>
	</div>
	<div class="panel-body">
        <table id="lastest_{{ $board }}" class="table table-condensed table-striped table-hover lastest">
            <thead>
                <tr>
                    <th>
                        @lang('lastest.title')
                    </th>
                    <th>
                        @lang('lastest.date')
                    </th>
                </tr>
            </thead>
            <tbody>
            @foreach ($writes as $w)
                <tr>
                    <td>
                        <a href="{{ board_url($board, $w['id']) }}" class="board-link visit">
                            {{ $w['subject'] }}
                        </a>
                    </td>
                    <td>
                        {{ $w['created_at'] }}
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
	</div>
</div>
