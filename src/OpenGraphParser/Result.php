<?php
namespace OpenGraphParser;
class Result {
    protected $content;
    protected $og_fields;
    protected $uri;
    protected $cacheAdapter;


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

    public function format($formatter) {
        $this->og_fields = $formatter($this->og_fields);
        return $this;
    }


    protected function build_og_fields() {
        $this->og_fields = array();
        $this->buildOpenGraph($this->content);
        $this->cacheOgFields();
    }

    //// PRIVATE ////
    //
    //


    private function cacheOgFields() {
        if(!is_null($this->cacheAdapter)) {
            $cachedData = array('content' => $this->content, 'openGraphFields' => $this->og_fields);
            $this->cacheAdapter->set($this->uri, $cachedData);
        }
    }

    private function buildOpenGraph($content) {
        $doc = $this->getDom($content);
        $metas = $this->getMetaFieldsStartingWith('og:', $doc);
        $this->og_fields = $this->extractAttributes($metas, 'og:');
        $this->buildSubType($doc, $this->og_fields);
    }

    private function buildSubType($doc, &$existingFields) {
        if(isset($existingFields['type'])) {
            $type = $existingFields['type'];
            $existingFields[$type] = array();

            $metas = $this->getMetaFieldsStartingWith($type.':', $doc);
            $existingFields[$existingFields['type']] = $this->extractAttributes($metas, $type.':');
        }
    }

    private function extractAttributes($metas, $removePrefix='') {
        $out = array();
        foreach ($metas as $meta) {
            $property = $meta->getAttribute('property');
            $content = $meta->getAttribute('content');
            $out[str_replace($removePrefix, '', $property)] = $content;
        }
        return $out;
    }

    private function getDom($content) {
        libxml_use_internal_errors(true);
        $doc = new \DomDocument();
        $doc->loadHTML($content);
        return $doc;
    }

    private function getMetaFieldsStartingWith($prefix, $fromDocument) {
        $xpath = new \DOMXPath($fromDocument);
        $query = '//*/meta[starts-with(@property, \''.$prefix.'\')]';
        $metas = $xpath->query($query);
        return $metas;
    }
}
