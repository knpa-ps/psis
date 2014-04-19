<?php 

class BoardService {

	public function getLastest($board, $count = 5, $subjectLength = 20) {

		$table = 'g5_write_'.$board;

		if (!Schema::hasTable($table)) {
			throw new Exception($table.' table does not exists');
		}

		$result = DB::table($table)->select(array(
			'wr_id', 
			'wr_subject', 
			'wr_comment', 
			'wr_name', 
			'wr_datetime'
			))->where('wr_is_comment', '=', 0)
		->orderBy('wr_num', 'asc')
		->take($count)->get();

		$writes = array();
		foreach ($result as $row) {
			$writes[] = array(
					'id' => $row->wr_id,
					'subject' => cut_str($row->wr_subject, $subjectLength),
					'num_comments' => $row->wr_comment,
					'author_name' => $row->wr_name,
					'created_at' => date('Y-m-d', strtotime($row->wr_datetime))
				);
		}

		return $writes;
	}

}