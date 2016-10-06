<?php namespace Process\Filter\Strategy;

use Process\Specification\Specification;
use Process\Specification\CompositeSpecification;

class NoFilter implements Specification
{
    use CompositeSpecification;

    public function isSatisfiedBy($row)
    {
        return true;
    }
}
