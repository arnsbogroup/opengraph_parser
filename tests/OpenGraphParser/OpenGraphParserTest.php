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

    public function testParseListReturnsAResultPerElement() {
        $subject = new OpenGraphParser();
        $result = $subject->parseList(array('', '', ''));

        $this->assertInstanceOf('OpenGraphParser\Result', $result[0]);
        $this->assertInstanceOf('OpenGraphParser\Result', $result[1]);
        $this->assertInstanceOf('OpenGraphParser\Result', $result[2]);
    }
}

