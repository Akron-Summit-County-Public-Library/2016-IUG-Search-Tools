<?php namespace Process\Specification;

class AndSpecification implements Specification
{
    use CompositeSpecification;

    protected $left;
    protected $right;

    public function __construct(Specification $left, Specification $right)
    {
        $this->left = $left;
        $this->right = $right;
    }

    public function isSatisfiedBy($candidate)
    {
        return ($this->left->isSatisfiedBy($candidate)
            && $this->right->isSatisfiedBy($candidate));
    }
}
