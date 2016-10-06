<?php namespace Sierra\Command\Sql\Pagination;

class PaginatedQueryCommand
{
    private $page = 1;
    private $limit = 5;

    public function setPage($page)
    {
        //--- Guard: Must be numeric ---//
        //--- Guard: No page below 1 ---//
        $this->page = $this->guard_input($page, 1);

        return $this;
    }

    public function setLimit($limit)
    {
        //--- Guard: Must be numeric ---//
        //--- Guard: No limit below 1 ---//
        $this->limit = $this->guard_input($limit, 5);

        return $this;
    }

    public function page()
    {
        return $this->page;
    }

    public function limit()
    {
        return $this->limit;
    }

    public function offset()
    {
        //--- From 1-based index to 0-based index ---//
        $page_index = ($this->page - 1);

        return ($page_index * $this->limit);
    }

    protected function guard_input($input, $default)
    {
        return (is_numeric($input) && (int)$input > 0)
            ? (int)$input
            : (int)$default;
    }
}
