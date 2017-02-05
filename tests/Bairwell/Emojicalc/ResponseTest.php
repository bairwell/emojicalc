<?php
namespace Bairwell\Emojicalc;

use PHPUnit\Framework\TestCase;

/**
 * Class ResponseTest
 * @package Bairwell\Emojicalc
 * @coversDefaultClass Bairwell\Emojicalc\Response
 * @uses \Bairwell\Emojicalc\Response
 */
class ResponseTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::setContentType
     * @covers ::getContentType
     */
    public function testContentType()
    {
        $sut = new Response();
        $this->assertSame('text/html;charset=utf-8', $sut->getContentType());
        $sut->setContentType('application/json');
        $this->assertSame('application/json', $sut->getContentType());
        $sut = new Response('jeff');
        $this->assertSame('jeff', $sut->getContentType());
    }

    /**
     * @covers ::getBody
     * @covers ::addToBody
     */
    public function testBody()
    {
        $sut = new Response();
        $this->assertSame('',$sut->getBody());
        $this->assertSame($sut,$sut->addToBody('test'));
        $this->assertSame('test',$sut->getBody());
        $this->assertSame($sut,$sut->addToBody('thing'));
        $this->assertSame('testthing',$sut->getBody());
    }

    /**
     * @covers ::reset
     */
    public function testReset() {
        $sut = new Response('abcdef');
        $this->assertSame('abcdef', $sut->getContentType());
        $this->assertSame('',$sut->getBody());
        $sut->addToBody('abc23def');
        $this->assertSame('abc23def',$sut->getBody());
        // do the reset
        $sut->reset('kjf');
        $this->assertSame('kjf', $sut->getContentType());
        $this->assertSame('',$sut->getBody());
        $sut->addToBody('abc23def');
        $this->assertSame('abc23def',$sut->getBody());
        // check default
        $sut->reset();
        $this->assertSame('text/html;charset=utf-8', $sut->getContentType());
        $this->assertSame('',$sut->getBody());
        $sut->addToBody('abc23def');
        $this->assertSame('abc23def',$sut->getBody());
    }
}