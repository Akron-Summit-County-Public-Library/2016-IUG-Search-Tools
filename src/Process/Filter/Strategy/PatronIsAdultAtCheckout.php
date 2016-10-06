<?php namespace Process\Filter\Strategy;

use Process\Specification\Specification;
use Process\Specification\CompositeSpecification;

use stdClass;
use DateTime;

class PatronIsAdultAtCheckout implements Specification
{
    use CompositeSpecification;

    public function isSatisfiedBy($row)
    {
        $birth_date = new DateTime($row->birth_date_gmt);
        $checkout_date = new DateTime($row->checkout_gmt);

        $years_after_born = $checkout_date->diff($birth_date)->y;

        return ($years_after_born >= 18);
    }
}
