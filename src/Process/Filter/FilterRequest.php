<?php namespace Process\Filter;

use Process\Specification\Specification;

use stdClass;

class FilterRequest
{
    protected $strategy;
    protected $id_column;

    public function __construct(Specification $strategy, $id_column)
    {
        $this->strategy = $strategy;
        $this->id_column = (string)$id_column;
    }

    public function isSatisfiedBy(stdClass $row)
    {
        return $this->strategy->isSatisfiedBy($row);
    }

    public function fetchId(stdClass $row)
    {
        $id_column = $this->id_column;
        return $row->$id_column;
    }
}
