<?php
namespace OpenGraphParser;
class Result {
    protected $content;
    protected $og_fields;
    protected $uri;
    protected $cacheAdapter;

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

        if(!is_null($this->cacheAdapter)) {
            $cachedData = array('content' => $this->content, 'openGraphFields' => $this->og_fields);
            $this->cacheAdapter->set($this->uri, $cachedData);
        }
    }

    public function __construct($data) {
        $this->uri = $data['uri'];
        $this->content = $data['content'];
        $this->og_fields = $data['openGraphFields'];
        $this->cacheAdapter = $data['cacheAdapter'];

        // it may be bad form to do this in a constructor, none-the-less, this is what I want
        //
        if($this->content != '') $this->getOpenGraphFields();
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

    public function getCacheAdapter() {
        return $this->cacheAdapter;
    }

    public function getUri() {
        return $this->uri;
    }
}
