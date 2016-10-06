<?php namespace Process\Filter;

class FilterResponse
{
    protected $request;

    protected $results;
    protected $unique_results;

    public function __construct(FilterRequest $request)
    {
        $this->request = $request;
    }

    public function __invoke($file_name = 'output')
    {
        $plaintext = file_get_contents("{$file_name}.json");
        $json_database = json_decode($plaintext);

        $results = array();
        $unique = array();

        $json_database = $json_database->entries;
        foreach ($json_database as $index => $row) {
            //--- Skip results that don't meet specifications ---//
            $result = $this->isSatisfiedBy($row);
            if ($result === false) {
                continue;
            }

            //--- Add unique specifications to separate array ---//
            $id = $this->fetchId($row);
            if (! isset($unique[$id])) {
                $unique[$id] = $row;
            }

            //--- Add all rows meeting specifications to results ---//
            $results[] = $row;
        }

        $this->unique_results = $unique;
        $this->results = $results;
    }

    public function uniqueResults()
    {
        return $this->unique_results;
    }

    public function results()
    {
        return $this->results;
    }

    public function countUniqueResults()
    {
        return count($this->uniqueResults());
    }

    public function countResults()
    {
        return count($this->results());
    }

    protected function fetchId($row)
    {
        return $this->request->fetchId($row);
    }

    protected function isSatisfiedBy($row)
    {
        return $this->request->isSatisfiedBy($row);
    }
}
