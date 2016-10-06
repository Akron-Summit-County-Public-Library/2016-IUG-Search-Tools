<?php namespace Process\Specification;

interface Specification
{
    public function isSatisfiedBy($candidate);

    public function and(Specification $other);
    public function or(Specification $other);
    public function not();
}
