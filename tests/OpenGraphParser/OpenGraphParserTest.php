<?php
namespace OpenGraphParser;
class OpenGraphParserTest extends \PHPUnit_Framework_TestCase
{
    public function testClassCanBeInstantiated() {
        $subject = new OpenGraphParser();
        $this->assertInstanceOf('OpenGraphParser\OpenGraphParser', $subject);
    }


    public function testParseUrlReturnsResultObject() {
        $subject = new OpenGraphParser();
        $result = $subject->parse();
        $this->assertInstanceOf('OpenGraphParser\Result', $result);
    }
}

