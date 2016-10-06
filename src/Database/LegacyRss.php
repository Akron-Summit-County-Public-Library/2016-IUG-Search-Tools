<?php namespace Database;

use SimpleXMLElement;
use stdClass;

class LegacyRss
{
    protected $metadata = array();

    protected $rss_items = array();

    public function __construct($content = '')
    {
        $this->metadata = new \stdClass;

        $this->deserializeText($content);
    }

    public function deserializeText($content)
    {
        if (! $content) {
            return;
        }

        $xml = new SimpleXMLElement($content);
        $properties = get_object_vars($xml);

        $this->extract($properties);
    }

    public function deserializeFile($content)
    {
        throw new \RuntimeException('Not yet implemented');
    }

    public function metadata()
    {
        return $this->metadata;
    }

    public function feed()
    {
        return $this->rss_items;
    }

    public function __get($key)
    {
        if (isset($this->metadata[$key]) === false) {
            return;
        }

        return $this->metadata[$key];
    }

    public function __set($key, $value)
    {
        // Overload: no-op
    }

    protected function extract($properties)
    {
        $output = array();
        $metadata = array();

        $required = 'channel';
        $this->guardRequired($required, $properties);
        $this->guardXml($required, $properties);

        foreach ($properties[$required] as $key => $value) {
            if ($key === 'item') {
                $output[] = $this->normalizeItem($value);
            } else {
                $metadata[$key] = $this->normalizeMetadata($value);
            }
        }

        $this->setFeed($output);
        $this->setMetadata($metadata);
    }

    protected function guardRequired($required, $value)
    {
        if (isset($value[$required])) return;

        throw new \RuntimeException(sprintf(
            'RSS Feed must contain the "%s" property',
            $required
        ));
    }

    protected function guardXml($required, $value)
    {
        if ($value[$required] instanceof SimpleXMLElement) return;

        throw new \InvalidArgumentException(sprintf(
            'RSS Feed "%s" property must be of type SimpleXMLElement, %s given',
            $required,
            is_object($value[$required]) ? get_class($value[$required]) : gettype($value[$required])
        ));
    }

    protected function normalizeItem($item)
    {
        $copy = $item;

        $copy->guid = $this->normalizeGuid($copy->guid);
        $copy->description = $this->normalizeDescription($copy->description);

        $copy = (object)get_object_vars($copy);

        return $copy;
    }

    protected function normalizeMetadata($data)
    {
        $copy = get_object_vars($data);
        return ($copy) ? $copy : (string)$data;
    }

    protected function normalizeGuid($guid)
    {
        $identifier = explode(' ', $guid);
        return reset($identifier);
    }

    protected function normalizeDescription($description)
    {
        return strip_tags($description);
    }

    protected function setMetadata(array $metadata)
    {
        $this->metadata = $metadata;
    }

    protected function setFeed(array $contents)
    {
        $this->rss_items = $contents;
    }
}
