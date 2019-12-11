<?php
namespace OpenGraphParser;
class OpenGraphParserTest extends \PHPUnit\Framework\TestCase
{

    public function setUp() : void {
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

    public function testParseListReturnsAResultCollection() {
        $result = $this->subject->parseList(array('', '', ''));
        $this->assertInstanceOf('OpenGraphParser\ResultCollection', $result);
    }

    public function testParseListReturnsACollectionWithCorrectCount() {
        $result = $this->subject->parseList(array('', '', ''));
        $this->assertEquals(3, count($result));
    }

    public function testParseListCanBeIteratedOver() {
        $result = $this->subject->parseList(array('', '', ''));
        $calls = 0;
        foreach($result as $element) {
            $this->assertInstanceOf('OpenGraphParser\Result', $element);
            $calls++;
        }
        $this->assertEquals(3, $calls);
    }

    public function testParseListCanBeFiltered() {
        $result = $this->subject->parseList(array('', '', ''));
        $noneSelected = $result->filter(function($elm) {
            return false;
        });

        $allSelected = $result->filter(function($elm) {
            return true;
        });

        $this->assertEquals(0, count($noneSelected));
        $this->assertEquals(3, count($allSelected));
    }

    public function testParseListCanBeFormatted() {
        $subject = OpenGraphParser::File();
        $fixturePath = realpath(__DIR__.'/../fixtures/simple.html');
        $result = $subject->parseList(array($fixturePath));
        $result->format(function($element) {
            foreach($element as $key=>$value) {
                $element[$key] = preg_replace("/[^a-z]/", '', $value);
            }
            return $element;
        });

        $this->assertEquals('httpthisistheurl', $result[0]->getOpenGraphFields()['url']);
        $this->assertEquals('asicage', $result[0]->getOpenGraphFields()['title']);
        $this->assertEquals('hisisabasicpageaboutstuff', $result[0]->getOpenGraphFields()['description']);
        $this->assertEquals('article', $result[0]->getOpenGraphFields()['type']);
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

    public function testArticleFieldsAreIncludedAsSubkey() {
        $subject = OpenGraphParser::File();
        $fixturePath = realpath(__DIR__.'/../fixtures/article.html');
        $result = $subject->parse($fixturePath);

        $fields = $result->getOpenGraphFields();

        $this->assertArrayHasKey('url', $fields);
        $this->assertEquals('http://clauswitt.com/how-to-write-a-cat-reading-files-in-c/', $fields['url']);

        $this->assertArrayHasKey('title', $fields);
        $this->assertArrayHasKey('description', $fields);
        $this->assertArrayHasKey('type', $fields);
        $this->assertArrayHasKey('article', $fields);
        $this->assertArrayHasKey('published_time', $fields['article']);
        $this->assertEquals('2015-01-02T09:46:25.000Z',  $fields['article']['published_time']);

    }

    /**
     * @expectedException OpenGraphParser\OpenGraphFetchException
     */
    public function testParseThrowsExceptionIfFetchStrategyDoes() {
        $strategy = $this->getMockBuilder('OpenGraphParser\FetchStrategy')
            ->setMethods(array('get_content'))
            ->getMock();


        $strategy->expects($this->once())
                 ->method('get_content')
                 ->with('something')
                 ->will($this->throwException(new OpenGraphFetchException));


        $this->subject->setFetchStrategy($strategy);


        $this->subject->parse('something');
    }

    public function testParseListOnlyReturnsResultsForEntriesWithoutExceptions() {
        $fixture1 = file_get_contents(realpath(__DIR__.'/../fixtures/article.html'));
        $fixture2 = file_get_contents(realpath(__DIR__.'/../fixtures/simple.html'));
        $map = array(
                array('first', $fixture1),
                array('second', $this->throwException(new OpenGraphFetchException())),
                array('third', $fixture2),
        );

        $strategy = $this->getMockBuilder('OpenGraphParser\FetchStrategy')
            ->setMethods(array('get_content'))
            ->getMock();

        $strategy->expects($this->any())
            ->method('get_content')
            ->will($this->returnValueMap($map));


        $this->subject->setFetchStrategy($strategy);


        $results = $this->subject->parseList(array('first','second','third'));
        $this->assertEquals(2, count($results));
    }

    public function testSettingNewCacheAdapterItAlsoShouldSetItOnTheFetchStrategy()
    {
        $this->subject->setCacheAdapter(new ArrayCacheAdapter);

        $this->assertInstanceOf('OpenGraphParser\ArrayCacheAdapter', $this->subject->getCacheAdapter());
        $this->assertInstanceOf('OpenGraphParser\ArrayCacheAdapter', $this->subject->getFetchStrategy()->getCacheAdapter());

        $this->subject->setCacheAdapter(new NoCacheAdapter);

        $this->assertInstanceOf('OpenGraphParser\NoCacheAdapter', $this->subject->getCacheAdapter());
        $this->assertInstanceOf('OpenGraphParser\NoCacheAdapter', $this->subject->getFetchStrategy()->getCacheAdapter());
    }

    public function testSettingNewFetchStrategyTheCacheAdapterShouldBeAssignedToIt()
    {
        $this->subject->setCacheAdapter(new ArrayCacheAdapter);
        $this->subject->setFetchStrategy(new FileFetchStrategy);

        $this->assertInstanceOf('OpenGraphParser\ArrayCacheAdapter', $this->subject->getFetchStrategy()->getCacheAdapter());

        $this->subject->setCacheAdapter(new NoCacheAdapter);
        $this->subject->setFetchStrategy(new FileFetchStrategy);

        $this->assertInstanceOf('OpenGraphParser\NoCacheAdapter', $this->subject->getFetchStrategy()->getCacheAdapter());
    }

}

