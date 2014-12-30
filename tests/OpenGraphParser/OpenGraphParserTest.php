<?php
namespace OpenGraphParser;
class OpenGraphParserTest extends \PHPUnit_Framework_TestCase
{

    public function setUp() {
        $this->subject = new OpenGraphParser();
    
    }
    public function testClassCanBeInstantiated() {
        $this->assertInstanceOf('OpenGraphParser\OpenGraphParser', $this->subject);
    }


    public function testParseUrlReturnsResultObject() {
        $result = $this->subject->parse('');
        $this->assertInstanceOf('OpenGraphParser\Result', $result);
    }

    public function testParseListReturnsAResultPerElement() {
        $result = $this->subject->parseList(array('', '', ''));

        $this->assertInstanceOf('OpenGraphParser\Result', $result[0]);
        $this->assertInstanceOf('OpenGraphParser\Result', $result[1]);
        $this->assertInstanceOf('OpenGraphParser\Result', $result[2]);
    }

    public function testParseUsesFetchStrategy() {

        $strategy = $this->getMockBuilder('OpenGraphParser\FetchStrategy')
            ->getMock();

        $strategy->expects($this->once())
                 ->method('get')
                 ->with($this->equalTo('something'));

        $this->subject->setFetchStrategy($strategy);


        $this->subject->parse('something');
    }

    public function testResultObjectHasOriginalFetchedBody() {
        $strategy = $this->getMockBuilder('OpenGraphParser\AbstractFetchStrategy')
            ->setMethods(array('get_content'))
            ->getMock();

        $strategy->expects($this->once())
                 ->method('get_content')
                 ->with($this->equalTo('something'))
                 ->willReturn('content');

        $this->subject->setFetchStrategy($strategy);

        $result = $this->subject->parse('something');

        $this->assertEquals('content', $result->getOriginalContent());
    }

    public function testStaticHttpMethodSetsFetchStrategyToHttp() {
        $subject = OpenGraphParser::Http();
        $this->assertInstanceOf('OpenGraphParser\HttpFetchStrategy', $subject->getFetchStrategy());
    }

    public function testStaticFileMethodSetsFetchStrategyToFile() {
        $subject = OpenGraphParser::File();
        $this->assertInstanceOf('OpenGraphParser\FileFetchStrategy', $subject->getFetchStrategy());
    }

    public function testFileParserGetsFixtureContent() {
        $subject = OpenGraphParser::File();
        $fixturePath = realpath(__DIR__.'/../fixtures/simple.html');
        $result = $subject->parse($fixturePath);
        $this->assertEquals(file_get_contents($fixturePath), $result->getOriginalContent());
    }

    public function testFileParserGetsOpenGraphFieldsFromFixture() {
        $subject = OpenGraphParser::File();
        $fixturePath = realpath(__DIR__.'/../fixtures/simple.html');
        $result = $subject->parse($fixturePath);
        $this->assertArrayHasKey('url', $result->getOpenGraphFields());
        $this->assertArrayHasKey('title', $result->getOpenGraphFields());
        $this->assertArrayHasKey('description', $result->getOpenGraphFields());
        $this->assertArrayHasKey('type', $result->getOpenGraphFields());
    }

    public function testParserHasCorrectFetchStrategyWhenSetByOptions() {
        $fileFetchStrategy = new FileFetchStrategy();
        $subject = new OpenGraphParser(array('fetchStrategy' => $fileFetchStrategy));
        $this->assertEquals($fileFetchStrategy, $subject->getFetchStrategy());
    }

    public function testParserHasCorrectCacheAdapterWhenSetByOptions() {
        $cacheAdapter = new NoCacheAdapter();
        $subject = new OpenGraphParser(array('cacheAdapter' => $cacheAdapter));
        $this->assertEquals($cacheAdapter, $subject->getCacheAdapter());
    }

    public function testParserChecksCacheForUriBeforeParsing() {
        $cacheAdapter = $this->getMockBuilder('OpenGraphParser\NoCacheAdapter')
            ->setMethods(array('has', 'get', 'set'))
            ->disableOriginalConstructor()
            ->getMock();

        $fileFetchStrategy = $this->getMockBuilder('OpenGraphParser\FileFetchStrategy')
            ->setMethods(array('get_content'))
            ->getMock();

        $subject = new OpenGraphParser(array('cacheAdapter' => $cacheAdapter, 'fetchStrategy' => $fileFetchStrategy));

        $fileFetchStrategy->expects($this->once())
            ->method('get_content')
            ->with($this->equalTo('some/url/here'))
            ->willReturn('some content');

        $cacheAdapter->expects($this->once())
            ->method('has')
            ->with($this->equalTo('some/url/here'))
            ->willReturn(false);

        $this->assertEquals($cacheAdapter, $subject->getCacheAdapter());
        $this->assertEquals($cacheAdapter, $subject->getFetchStrategy()->getCacheAdapter());

        $subject->parse('some/url/here');
        
    }

    public function testParserSetsResultOfParsingInCache() {
        $fixturePath = realpath(__DIR__.'/../fixtures/simple.html');

        $cacheAdapter = $this->getMockBuilder('OpenGraphParser\NoCacheAdapter')
            ->setMethods(array('has', 'get', 'set'))
            ->disableOriginalConstructor()
            ->getMock();

        $cacheAdapter->expects($this->once())
            ->method('has')
            ->with($this->equalTo($fixturePath))
            ->willReturn(false);

        $cacheAdapter->expects($this->once())
            ->method('set')
            ->with($this->equalTo($fixturePath), $this->logicalAnd($this->arrayHasKey('content'), $this->arrayHasKey('openGraphFields')));


        $subject = OpenGraphParser::File(array('cacheAdapter' => $cacheAdapter));

        $this->assertEquals($cacheAdapter, $subject->getCacheAdapter());
        $this->assertEquals($cacheAdapter, $subject->getFetchStrategy()->getCacheAdapter());

        $result = $subject->parse($fixturePath);
        $this->assertEquals($cacheAdapter, $result->getCacheAdapter());

        $result->getOpenGraphFields();
    }

    public function testParserReturnsResultFromCacheIfPresent() {
        $fixturePath = realpath(__DIR__.'/../fixtures/simple.html');

        $cacheAdapter = $this->getMockBuilder('OpenGraphParser\NoCacheAdapter')
            ->setMethods(array('has', 'get', 'set'))
            ->disableOriginalConstructor()
            ->getMock();

        $cacheAdapter->expects($this->once())
            ->method('has')
            ->with($this->equalTo($fixturePath))
            ->willReturn(true);

        $cacheAdapter->expects($this->once())
            ->method('get')
            ->with($this->equalTo($fixturePath))
            ->willReturn(array('content' => 'basic content here', 'openGraphFields' => array('title' => 'new title here')));


        $subject = OpenGraphParser::File(array('cacheAdapter' => $cacheAdapter));

        $this->assertEquals($cacheAdapter, $subject->getCacheAdapter());
        $this->assertEquals($cacheAdapter, $subject->getFetchStrategy()->getCacheAdapter());

        $result = $subject->parse($fixturePath);
        $this->assertEquals($cacheAdapter, $result->getCacheAdapter());

        $ogFields = $result->getOpenGraphFields();
        $this->assertEquals('new title here', $ogFields['title']);
    }

    public function testParserOnlyFetchesContentOnceIfUriIsCached() {
        $cacheAdapter = new ArrayCacheAdapter();

        $fileFetchStrategy = $this->getMockBuilder('OpenGraphParser\FileFetchStrategy')
            ->setMethods(array('get_content'))
            ->getMock();

        $subject = new OpenGraphParser(array('cacheAdapter' => $cacheAdapter, 'fetchStrategy' => $fileFetchStrategy));

        $fileFetchStrategy->expects($this->once())
            ->method('get_content')
            ->with($this->equalTo('some/url/here'))
            ->willReturn('some content');

        $this->assertEquals($cacheAdapter, $subject->getCacheAdapter());
        $this->assertEquals($cacheAdapter, $subject->getFetchStrategy()->getCacheAdapter());

        $results = $subject->parseList(array('some/url/here','some/url/here','some/url/here'));
    }
}

