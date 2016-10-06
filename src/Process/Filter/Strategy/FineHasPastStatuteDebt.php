<?php namespace Process\Filter\Strategy;

use Process\Specification\Specification;
use Process\Specification\CompositeSpecification;

use stdClass;

class FineHasPastStatuteDebt implements Specification
{
    use CompositeSpecification;

    public function isSatisfiedBy($row)
    {
        return ($row->past_statute_debt > 0);
    }
}
