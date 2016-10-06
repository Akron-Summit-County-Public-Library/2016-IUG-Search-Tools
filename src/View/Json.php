<?php namespace View;

class Json
{
    protected $json_options = 0;

    protected $content = '';

    public function __construct($options = 0)
    {
        $this->options = ($options !== 0)
            ? $options
            : JSON_PRETTY_PRINT
                | JSON_UNESCAPED_UNICODE
                | JSON_UNESCAPED_SLASHES
        ;
    }

    public function __invoke($value)
    {
        $options = $this->options();

        $output = (object)array(
            'total' => count($value),
            'entries' => (object)$value
        );

        $this->content = json_encode($output, $options);

        return $this;
    }

    public function toFile($file_name = '')
    {
        $file_name = ($file_name) ? "{$file_name}.json" : 'php://output';

        $file = fopen($file_name, 'w');
        fwrite($file, $this->content);
        fclose($file);
    }

    public function toString()
    {
        return $this->content;
    }

    public function __toString()
    {
        return $this->content;
    }

    protected function options()
    {
        return $this->options;
    }
}
