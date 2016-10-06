<?php namespace Database;

class SqlQuery
{
    protected $path = '';
    protected $content = '';
    protected $folder_name = '';

    public function __construct($folder_name = '*')
    {
        $this->setFolder($folder_name);
    }

    public function path()
    {
        return $this->path;
    }

    public function fetchFile($file)
    {
        //--- Fetch SQL Queries ---//
        $folder_name = $this->folder();

        $sql_files = glob($folder_name . '/*.sql');
        foreach ($sql_files as $next_file) {
            $file_parts = explode('/', $next_file);
            $name = str_replace(
                '.sql',
                '',
                end($file_parts)
            );

            if (strtolower($file) !== strtolower($name)) {
                continue;
            }

            $this->setPath($next_file);
            $this->parseContent();
            break;
        }

        return $this;
    }

    public function withContent(array $template = array())
    {
        $content = $this->content;

        foreach ($template as $key => $value) {
            if (is_string($value) === false) { continue; }
            $content = str_ireplace('{$' . $key . '}', $value, $content);
        }

        return $content;
    }

    protected function setFolder($folder_name)
    {
        $this->folder = $folder_name;
    }

    protected function folder()
    {
        return $this->folder;
    }

    protected function setPath($path)
    {
        $this->path = $path;
    }

    protected function parseContent()
    {
        $path = $this->path();

        $this->content = file_get_contents($path);
    }
}
