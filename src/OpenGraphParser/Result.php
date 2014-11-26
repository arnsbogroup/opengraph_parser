<?php
namespace OpenGraphParser;
class Result {
    protected $content;
    protected $og_fields;

    protected function build_og_fields() {
        $this->og_fields = array();
        libxml_use_internal_errors(true);
        $doc = new \DomDocument();
        $doc->loadHTML($this->content);
        $xpath = new \DOMXPath($doc);
        $query = '//*/meta[starts-with(@property, \'og:\')]';
        $metas = $xpath->query($query);
        foreach ($metas as $meta) {
            $property = $meta->getAttribute('property');
            $content = $meta->getAttribute('content');
            $this->og_fields[str_replace('og:', '', $property)] = $content;
        }
    }

    public function __construct($content) {
        $this->content = $content;
    }

    public function getOpenGraphFields() {
        if(is_null($this->og_fields) || empty($this->og_fields)) {
            $this->build_og_fields();
        }
        return $this->og_fields;
    }

    public function getOriginalContent() {
        return $this->content;
    }
}
