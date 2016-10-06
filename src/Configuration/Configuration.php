<?php namespace Configuration;

class Configuration
{
    protected $config;

    public function config()
    {
        return $this->config;
    }

    public function addConfigFile($file_path)
    {
        $serialize = $this->file_to_json($file_path);
        $this->merge($serialize);

        $this->config = (object)$this->config;

        return $this;
    }

    public function forceConfigFile($file_path)
    {
        $serialize = $this->file_to_json($file_path);
        $this->merge($serialize, false);

        $this->config = (object)$this->config;

        return $this;
    }

    protected function file_to_json($file_path)
    {
        $file = "{$file_path}.json";

        $exists = file_exists($file);
        if ($exists === false) {
            return array();
        }

        $plaintext = file_get_contents($file);

        return json_decode($plaintext);
    }

    protected function merge($serialized, bool $append_numeric = true)
    {
        if (! is_array($serialized) && ! is_object($serialized)) {
            return;
        }

        foreach ($serialized as $key => $value)
        {
            //--- Append if true flag ---//
            if ($append_numeric && $this->append($key, $value)) {
                continue;
            }

            //--- Overwrite object properties ---//
            if ($this->merge_object($key, $value)) {
                continue;
            }

            //--- Merge object property array values recursively ---//
            if ($this->merge_property_array($key, $value)) {
                continue;
            }

            //--- Merge array values recursively ---//
            if ($this->merge_array($key, $value)) {
                continue;
            }

            //--- Overwrite if false flag ---//
            $this->overwrite($key, $value);
        }

        return;
    }

    protected function append($key, $value)
    {
        if (! is_int($key)) {
            return false;
        }

        $this->config[] = $value;

        return true;
    }

    protected function overwrite($key, $value)
    {
        $this->config[$key] = $value;

        return true;
    }

    protected function merge_array($key, $value)
    {
        if (! isset($this->config[$key])) {
            return false;
        }

        if (! is_array($this->config[$key])) {
            return false;
        }

        if (! is_array($value)) {
            return false;
        }

        $this->config[$key] = static::merge($value);

        return true;
    }

    protected function merge_property_array($key, $value)
    {
        if (! isset($this->config->$key)) {
            return false;
        }

        if (! is_array($this->config->$key)) {
            return false;
        }

        if (! is_array($value)) {
            return false;
        }

        $this->config->$key = static::merge($value);

        return true;
    }

    protected function merge_object($key, $value)
    {
        if (! isset($this->config->$key)) {
            return false;
        }

        if (! is_object($this->config->$key)) {
            return false;
        }

        if (! is_object($value)) {
            return false;
        }

        $this->config->$key = $value;

        return true;
    }
}
