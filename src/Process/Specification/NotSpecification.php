<?php namespace Process\Specification;

class NotSpecification implements Specification
{
    use CompositeSpecification;

    protected $spec;

    public function __construct(Specification $spec)
    {
        $this->spec = $spec;
    }

    public function isSatisfiedBy($candidate)
    {
        return ($this->spec->isSatisfiedBy($candidate) === false);
    }
}
