<?php
namespace Bairwell\Emojicalc;

use PHPUnit\Framework\TestCase;

/**
 * Class RequestTest
 * @package Bairwell\Emojicalc
 * @coversDefaultClass Bairwell\Emojicalc\Request
 * @uses \Bairwell\Emojicalc\Request
 */
class RequestTest extends TestCase
{
    /**
     * @covers ::getContentType
     * @covers ::withContentType
     * @covers ::setUp
     * @covers ::__construct
     */
    public function testContentType() {
        $sut=new Request([],[],[],function() { return ''; });
        $this->assertSame(RequestInterface::DEFAULTCONTENTTYPE,$sut->getContentType());
        $this->assertSame($sut,$sut->withContentType('application/json'));
        $this->assertSame('application/json',$sut->getContentType());
        $env=['CONTENT_TYPE'=>'hello/There'];
        $sut=new Request($env,[],[],function() { return ''; });
        $this->assertSame('hello/There',$sut->getContentType());
    }
    /**
     * @covers ::setJson
     * @covers ::isJson
     */
    public function testIsJson() {
        $sut=new Request([],[],[],function() { return ''; });
        $this->assertFalse($sut->isJson());
        $this->assertSame($sut,$sut->setJson(true));
        $this->assertTrue($sut->isJson());
        $this->assertSame($sut,$sut->setJson(false));
        $this->assertFalse($sut->isJson());
    }

    /**
     * @covers ::isJson
     * @covers ::setUp
     * @covers ::getParsedBody
     */
    public function testJson() {
        $env=['CONTENT_TYPE'=>'application/json'];
        $testData=['a'=>'b','c'=>2];
        $postData=['text'=>'xyz'];
        $callable=function() use ($testData) { return json_encode($testData);};
        $sut=new Request($env,[],$postData,$callable);
        $this->assertTrue($sut->isJson());
        $this->assertSame($testData,$sut->getParsedBody());
    }
    /**
     * @covers ::isJson
     * @covers ::setUp
     * @covers ::getParsedBody
     */
    public function testInvalidJson() {
        $env=['CONTENT_TYPE'=>'application/json'];
        $testData=['a'=>'b','c'=>2];
        $postData=['text'=>'xyz'];
        $callable=function() use ($testData) { return 'rubbish'; };
        $sut=new Request($env,[],$postData,$callable);
        $this->assertFalse($sut->isJson());
        $this->assertSame($postData,$sut->getParsedBody());
    }
    /**
     * @covers ::getUri
     * @covers ::withUri
     * @covers ::setUp
     * @covers ::__construct
     */
    public function testUrl() {
        $sut=new Request([],[],[],function() { return ''; });
        $this->assertSame('',$sut->getUri());
        $this->assertSame($sut,$sut->withUri('http://example.com'));
        $this->assertSame('http://example.com',$sut->getUri());
        $env=['REQUEST_URI'=>'http://example.com/here/we/go'];
        $sut=new Request($env,[],[],function() { return ''; });
        $this->assertSame('http://example.com/here/we/go',$sut->getUri());
    }
    /**
     * @covers ::getMethod
     * @covers ::withMethod
     * @covers ::setUp
     * @covers ::__construct
     */
    public function testMethod() {
        $sut=new Request([],[],[],function() { return ''; });
        $this->assertSame(RequestInterface::DEFAULTMETHOD,$sut->getMethod());
        $this->assertSame($sut,$sut->withMethod('GET'));
        $this->assertSame('GET',$sut->getMethod());
        $env=['REQUEST_METHOD'=>'hello'];
        $sut=new Request($env,[],[],function() { return ''; });
        $this->assertSame('HELLO',$sut->getMethod());

    }
    /**
     * @covers ::getParsedBody
     * @covers ::withParsedBody
     */
    public function testParsedBody() {
        $sut=new Request([],[],[],function() { return ''; });
        $this->assertSame([],$sut->getParsedBody());
        $this->assertSame($sut,$sut->withParsedBody(['a','b']));
        $this->assertSame(['a','b'],$sut->getParsedBody());
    }
    /**
     * @covers ::getQueryParams
     * @covers ::withQueryParams
     */
    public function testQueryParams() {
        $sut=new Request([],[],[],function() { return ''; });
        $this->assertSame([],$sut->getQueryParams());
        $this->assertSame($sut,$sut->withQueryParams(['a','b']));
        $this->assertSame(['a','b'],$sut->getQueryParams());
    }
    /**
     * @covers ::getPathParameters
     * @covers ::withPathParameters
     */
    public function testPathParameters() {
        $sut=new Request([],[],[],function() { return ''; });
        $this->assertSame([],$sut->getPathParameters());
        $this->assertSame($sut,$sut->withPathParameters(['a','b']));
        $this->assertSame(['a','b'],$sut->getPathParameters());
    }

}