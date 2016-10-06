<?php namespace Database;

class CsvDatabase
{
    protected $value;
    protected $items = array();

    public function __construct($input_string='')
    {
        $this->value = (string)$input_string;
    }

    public function fromFile($input_file_path='')
    {
        $this->value = file_get_contents($input_file_path);

        return $this;
    }

    public function toArray($with_headers = false)
    {
        $items = $this->items;
        if (!$items) {
            $this->parseCsv($this->value);

            $items = $this->items;
        }

        if ($with_headers) {
            $items = $this->withHeaders();
        }

        return $items;
    }

    protected function withHeaders()
    {
        $output = $this->items;

        if (!$output) {
            return $output;
        }

        array_walk(
            $output,
            function(&$temp) use ($output) {
                $temp = array_combine($output[0], $temp);
            }
        );

        array_shift($output);
        return $output;
    }

    protected function parseCsv($input)
    {
        $data = explode("\n", $input);
        $data = array_filter($data);

        foreach ($data as &$row) {
            $row = str_getcsv($row, ',');
        }

        $this->items = $data;
    }
}
