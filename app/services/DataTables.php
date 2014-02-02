<?php 

class DataTables {
	
	private $query;
	private $columns = array();

	public function __construct($query)
	{
		$this->query = $query;
		$this->columns = $query->columns;
	}

    private function paging()
    {
        if(!is_null(Input::get('iDisplayStart')) && Input::get('iDisplayLength') != -1)
        {
            $this->query->skip(Input::get('iDisplayStart'))->take(Input::get('iDisplayLength',10));
        }
    }

    private function ordering()
    {
        if(!is_null(Input::get('iSortCol_0')))
        {
            for ( $i=0, $c=intval(Input::get('iSortingCols')); $i<$c ; $i++ )
            {
                if ( Input::get('bSortable_'.intval(Input::get('iSortCol_'.$i))) == "true" )
                {
                    if(isset($this->columns[intval(Input::get('iSortCol_'.$i))]))
                    {
                    	$this->query->orderBy($this->columns[intval(Input::get('iSortCol_'.$i))],Input::get('sSortDir_'.$i));
                    }
                }
            }
        }
    }

    private function filtering()
    {
        if (Input::get('sSearch','') != '')
        {
            $copy_this = $this;
            $copy_this->columns = $this->columns;

            $this->query->where(function($query) use ($copy_this) {

                $db_prefix = $copy_this->database_prefix();

                for ($i=0,$c=count($copy_this->columns);$i<$c;$i++)
                {
                    if (Input::get('bSearchable_'.$i) == "true")
                    {
                        $column = $copy_this->columns[$i];

                        if (stripos($column, ' AS ') !== false){
                            $column = substr($column, stripos($column, ' AS ')+4);
                        }

                        $keyword = '%'.Input::get('sSearch').'%';

						$cast_begin = null;
                        $cast_end = null;

                        $column = $db_prefix . $column;
                        $query->orwhere(DB::raw('LOWER('.$column.')'), 'LIKE', strtolower($keyword));
                    }
                }
            });

        }

        $db_prefix = $this->database_prefix();

        for ($i=0,$c=count($this->columns);$i<$c;$i++)
        {
            if (Input::get('bSearchable_'.$i) == "true" && Input::get('sSearch_'.$i) != '')
            {
                $keyword = '%'.Input::get('sSearch_'.$i).'%';
                $column = $db_prefix . $columns[$i];
                $this->query->where(DB::raw('LOWER('.$column.')'),'LIKE', strtolower($keyword));
            }
        }
    }
}