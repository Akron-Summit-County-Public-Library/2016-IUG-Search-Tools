<?php namespace Process\Specification;

trait CompositeSpecification
{
    public function and(Specification $other)
    {
        return new AndSpecification($this, $other);
    }

    public function or(Specification $other)
    {
        return new OrSpecification($this, $other);
    }

    public function not()
    {
        return new NotSpecification($this);
    }
}
