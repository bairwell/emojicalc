<?php

namespace Bairwell\Emojicalc;
use PHPUnit\Framework\TestCase;

/**
 * Class ContainerTest
 * @package Bairwell\Emojicalc
 * @coversDefaultClass Bairwell\Emojicalc\Container
 * @uses \Bairwell\Emojicalc\Container
 */
class ContainerTest extends TestCase
{
    /**
     * Test the constructor to ensure we get a container supporting array interface back.
     * @covers ::__construct
     */
    public function testConstructor() {
        $sut=new Container();
        $this->assertInstanceOf(ContainerInterface::class,$sut);
    }
    /**
     * @covers ::has
     * @covers ::offsetSet
     */
    public function testHasAndInsert() {
        $sut=new Container();
        $this->assertFalse($sut->has('jeff'));
        $sut['jeff']='abc';
        $this->assertTrue($sut->has('jeff'));
    }

    /**
     * @covers ::get
     * @expectedException \RuntimeException
     * @expectedExceptionMessage No entry was found for "hello" identifier.
     */
    public function testGetNoExists() {
        $sut=new Container();
        $sut->get('hello');
    }
    /**
     * @covers ::has
     * @covers ::get
     * @covers ::offsetSet
     */
    public function testHasGetAndInsert() {
        $sut=new Container();
        $this->assertFalse($sut->has('jeff'));
        $sut['jeff']='abc';
        $this->assertTrue($sut->has('jeff'));
        $this->assertSame('abc',$sut->get('jeff'));
    }

    /**
     * @covers ::offsetUnset
     */
    public function testUnset() {
        $sut=new Container();
        $this->assertFalse($sut->has('jeff'));
        $sut['jeff']='abc';
        $this->assertTrue($sut->has('jeff'));
        $this->assertSame('abc',$sut->get('jeff'));
        unset($sut['jeff']);
        $this->assertFalse($sut->has('jeff'));
    }
    /**
     * @covers ::offsetExists
     * @expectedException \RuntimeException
     * @expectedExceptionMessage offsetExists on containers cannot be used directly. Use "has"
     */
    public function testOffsetExists() {
        $sut=new Container();
        $sut['jeff']='bs';
        $a=isset($sut['jeff']);
        $this->fail('Should not have a set: returned '.$a);
    }

    /**
     * @covers ::offsetGet
     * @expectedException \RuntimeException
     * @expectedExceptionMessage offsetGet on containers cannot be used directly. Use "get"
     */
    public function testOffsetGet() {
        $sut=new Container();
        $sut['jeff']='bs';
        $a=$sut['jeff'];
        $this->fail('Should not have a set: returned '.$a);
    }
}