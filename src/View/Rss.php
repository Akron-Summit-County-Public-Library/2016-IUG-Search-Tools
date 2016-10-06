<?php namespace View;

use DOMDocument;
use DOMElement;


/**
 * Converts array to RSS 2.0 compliant webpage
 *
 * Long description
 *
 * @author Cameron Schrode <cschrode@akronlibrary.org>
 */
class Rss
{
    protected $document;

    protected $link;
    protected $metadata = array();
    protected $feed = array();

    protected $content = '';

    public function __construct()
    {
        $document = new DOMDocument('1.0', 'utf-8');
        $document->formatOutput = true;

        $this->document = $document;
    }

    public function __invoke(array $feed, $link, $metadata = array())
    {
        $this->feed = $feed;
        $this->link = (string)$link;
        $this->metadata = $metadata;

        $namespace = $this->namespace();
        $channel = $this->channel();
        $atom_link = $this->atomLink();

        $document = $this->withElement($namespace)
            ->withElement($channel)
            ->withElement($atom_link, $channel);

        foreach ($this->metadataElements() as $element) {
            $document = $this->withElement($element, $channel);
        }

        foreach ($this->feedItems() as $element) {
            $document = $this->withElement($element, $channel);
        }

        $this->content = $this->document->saveXML();

        return $this;
    }

    public function toFile($file_name = '')
    {
        $file_name = ($file_name) ? "{$file_name}.xml" : 'php://output';

        $file = fopen($file_name, 'w');
        fwrite($file, $content);
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

    protected function namespace()
    {
        $line = $this->document->createElement('rss');

        $line->setAttribute('version', '2.0');
        $line->setAttribute('xmlns:atom', 'http://www.w3.org/2005/Atom');

        return $line;
    }

    protected function channel()
    {
        return $this->document->createElement('channel');
    }

    protected function atomLink()
    {
        $line = $this->document->createElement('atom:link');

        $link = $this->link();
        $line->setAttribute('href', $link);
        $line->setAttribute('rel', 'self');
        $line->setAttribute('type', 'application/rss+xml');

        return $line;
    }

    protected function metadataElements()
    {
        $output = array();

        foreach ($this->metadata() as $key => $value)
        {
            if (is_array($value)) {
                $content = $this->document->createElement($key);
                $this->apply($content, $value);
            }

            if (is_string($value)) {
                $content = $this->document->createElement($key, $value);
            }

            $output[] = $content;
        }

        return $output;
    }

    protected function feedItems()
    {
        $output = array();

        foreach ($this->feed() as $value)
        {
            $content = $this->document->createElement('item');

            $this->apply($content, $value);

            $output[] = $content;
        }

        return $output;
    }

    protected function apply($parent, $value)
    {
        foreach ($value as $attribute => $content)
        {
            $content = htmlentities($content);
            $element = $this->document->createElement($attribute, $content);
            $parent->appendChild($element);
        }

        return $element;
    }

    protected function associativeArrayElement($key, $value)
    {
        $element = $this->document->createElement($key);
        foreach ($value as $attribute => $content)
        {
            $content = htmlentities($content);
            $element->setAttribute($attribute, $content);
        }

        return $element;
    }

    protected function withElement(DOMElement $element, DOMElement $parent = null)
    {
        if ($parent) {
            $parent->appendChild($element);
        } else {
            $this->document->appendChild($element);
        }

        return $this;
    }

    protected function metadata()
    {
        return $this->metadata;
    }

    protected function feed()
    {
        return $this->feed;
    }

    protected function link()
    {
        return $this->link;
    }
}
