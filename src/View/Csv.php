<?php namespace View;

class Csv
{
    protected $file_name = 'output';
    protected $attachment = false;
    protected $headers = true;
    protected $append = false;

    public function __construct($attachment = false, $headers = true, $append = false)
    {
        $this->attachment = $attachment;
        $this->headers = $headers;
        $this->append = (!$headers && $append);
    }

    public function __invoke($value)
    {
        $this->items = $value;

        return $this;
    }

    public function toFile($file_name = '')
    {
        $value = $this->items;

        $format_as_file = (strtolower($file_name) !== 'php://memory');
        if ($format_as_file) {
            $file_name = ($file_name) ? "{$file_name}.csv" : 'php://output';
        }

        $attachment = $this->attachment();
        $headers = $this->headers();


        if($format_as_file && $attachment) {
            header('Content-Type: text/csv');
            header("Content-Disposition: attachment;filename={$filename}");
            $file_handle = fopen('php://output', 'w');
        } else {
            $file_mode = ($this->append) ? 'a' : 'w';
            $file_handle = fopen($file_name, $file_mode);
        }

        $row = reset($value);
        if($headers && $row) {
            if (is_object($row)) {
                $row = get_object_vars($row);
            }

            $headers = array_keys($row);
            fputcsv($file_handle, $headers);
        }

        foreach($value as $row) {
            if (is_object($row)) {
                $row = get_object_vars($row);
            }

            fputcsv($file_handle, $row);
        }

        if ($format_as_file) {
            fclose($file_handle);
            return;
        }

        rewind($file_handle);
        $content = stream_get_contents($file_handle);
        fclose($file_handle);

        return $content;
    }

    public function toString()
    {
        return $this->toFile('php://memory');
    }

    public function __toString()
    {
        return $this->toFile('php://memory');
    }

    protected function attachment()
    {
        return $this->attachment;
    }

    protected function headers()
    {
        return $this->headers;
    }
}
